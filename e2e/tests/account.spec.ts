import * as OTPAuth from 'otpauth';
import { test, expect } from '@playwright/test';
import { SEED } from '../constants';
import { logout } from '../helpers/auth';
import { verificationUrl } from '../helpers/artisan';
import { backendBaseURL, gotoHydrated, loginViaApi, registerViaApi } from '../helpers/nuxtAuth';

// Profile/account settings coverage (issue #19, Phase 4). See dashboard.spec.ts
// for why auth is established via the API rather than a Nuxt login page.

function totpCode(secretKey: string): string {
  const totp = new OTPAuth.TOTP({
    secret: OTPAuth.Secret.fromBase32(secretKey),
    algorithm: 'SHA1',
    digits: 6,
    period: 30,
  });

  return totp.generate();
}

test.describe('Nuxt profile and account', () => {
  test('update profile information', async ({ page }) => {
    await loginViaApi(page);
    await gotoHydrated(page, '/user/profile');

    const form = page.getByTestId('profile-information-form');
    await form.locator('#name').fill('Test Admin Updated');
    await form.getByRole('button', { name: 'Save' }).click();
    await expect(form.getByText('Saved.')).toBeVisible();

    await form.locator('#name').fill(SEED.users.admin.name);
    await form.getByRole('button', { name: 'Save' }).click();
    await expect(form.getByText('Saved.')).toBeVisible();
  });

  test('update password', async ({ page }) => {
    const email = `e2e-nuxt-password-${Date.now()}@example.com`;
    await registerViaApi(page, { name: 'Password Changer', email, password: 'password' });
    await page.goto(verificationUrl(email));

    await gotoHydrated(page, '/user/profile');
    const form = page.getByTestId('update-password-form');
    await form.locator('#current_password').fill('password');
    await form.locator('#password').fill('password-next');
    await form.locator('#password_confirmation').fill('password-next');
    await form.getByRole('button', { name: 'Save' }).click();
    await expect(form.getByText('Saved.')).toBeVisible();

    await page.goto('/');
    await logout(page);
    await loginViaApi(page, { email, password: 'password-next' });
  });

  test('log out other browser sessions', async ({ page }) => {
    await loginViaApi(page);
    await gotoHydrated(page, '/user/profile');
    await page.getByRole('button', { name: 'Log Out Other Browser Sessions' }).click();
    await page.getByPlaceholder('Password').fill(SEED.users.admin.password);
    await page.getByTestId('confirm-password-submit').click();
    await expect(page.getByText('Done.')).toBeVisible();
  });

  test('enable and disable two-factor authentication', async ({ page }) => {
    const email = `e2e-nuxt-2fa-${Date.now()}@example.com`;
    await registerViaApi(page, { name: 'Two Factor User', email, password: 'password' });
    await page.goto(verificationUrl(email));

    await gotoHydrated(page, '/user/profile');
    await page.getByTestId('two-factor-enable').click();
    await page.getByPlaceholder('Password').fill('password');
    await page.getByTestId('confirm-password-submit').click();
    await expect(page.getByText('You have enabled two factor authentication.')).toBeVisible();

    const secretResponse = await page.request.get(`${backendBaseURL()}/api/user/two-factor-secret-key`);
    expect(secretResponse.ok()).toBeTruthy();
    const { secretKey } = await secretResponse.json();
    expect(totpCode(secretKey)).toMatch(/^\d{6}$/);

    await page.getByTestId('two-factor-disable').click();
    await page.getByPlaceholder('Password').fill('password');
    await page.getByTestId('confirm-password-submit').click();
    await expect(page.getByText('You have not enabled two factor authentication.')).toBeVisible();
  });

  test('delete account', async ({ page }) => {
    const email = `e2e-nuxt-delete-${Date.now()}@example.com`;
    await registerViaApi(page, { name: 'Delete Me', email, password: 'password' });
    await page.goto(verificationUrl(email));

    await gotoHydrated(page, '/user/profile');
    await page.getByRole('button', { name: 'Delete Account' }).click();
    await page.getByPlaceholder('Password').fill('password');
    await page.getByTestId('confirm-password-submit').click();
    await expect(page).toHaveURL('/');

    await expect(loginViaApi(page, { email, password: 'password' })).rejects.toThrow();
  });
});
