import * as OTPAuth from 'otpauth';
import { test, expect } from '@playwright/test';
import { SEED } from '../constants';
import { loginAs, logout, openUserMenu } from '../helpers/auth';
import { verificationUrl } from '../helpers/artisan';

function totpCode(secretKey: string): string {
  const totp = new OTPAuth.TOTP({
    secret: OTPAuth.Secret.fromBase32(secretKey),
    algorithm: 'SHA1',
    digits: 6,
    period: 30,
  });

  return totp.generate();
}

test.describe('Profile and account', () => {
  test('update profile information', async ({ page }) => {
    await loginAs(page);
    await page.goto('/user/profile');

    const form = page.getByTestId('profile-information-form');
    await form.locator('#name').fill('Test Admin Updated');
    await form.getByRole('button', { name: 'Save' }).click();
    await expect(form.getByText('Saved.')).toBeVisible();

    await form.locator('#name').fill(SEED.users.admin.name);
    await form.getByRole('button', { name: 'Save' }).click();
    await expect(form.getByText('Saved.')).toBeVisible();
  });

  test('update password', async ({ page }) => {
    const email = `e2e-password-${Date.now()}@example.com`;
    await page.goto('/register');
    await page.locator('#name').fill('Password Changer');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill('password');
    await page.locator('#password_confirmation').fill('password');
    await page.locator('#registration_secret').fill(SEED.registrationSecret);
    await page.getByTestId('register-submit').click();
    await expect(page).toHaveURL(/\/email\/verify/);

    await page.goto(verificationUrl(email));

    await page.goto('/user/profile');
    const form = page.getByTestId('update-password-form');
    await form.locator('#current_password').fill('password');
    await form.locator('#password').fill('password-next');
    await form.locator('#password_confirmation').fill('password-next');
    await form.getByRole('button', { name: 'Save' }).click();
    await expect(form.getByText('Saved.')).toBeVisible();

    await logout(page);
    await loginAs(page, { email, password: 'password-next' });
  });

  test('log out other browser sessions', async ({ page }) => {
    await loginAs(page);
    await page.goto('/user/profile');
    await page.getByRole('button', { name: 'Log Out Other Browser Sessions' }).first().click();
    await page.getByPlaceholder('Password').fill(SEED.users.admin.password);
    await page.getByRole('button', { name: 'Log Out Other Browser Sessions' }).last().click();
    await expect(page.getByText('Done.')).toBeVisible();
  });

  test('enable and disable two-factor authentication', async ({ page }) => {
    const email = `e2e-2fa-${Date.now()}@example.com`;
    await page.goto('/register');
    await page.locator('#name').fill('Two Factor User');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill('password');
    await page.locator('#password_confirmation').fill('password');
    await page.locator('#registration_secret').fill(SEED.registrationSecret);
    await page.getByTestId('register-submit').click();
    await expect(page).toHaveURL(/\/email\/verify/);

    await page.goto(verificationUrl(email));

    await page.goto('/user/profile');
    await page.getByTestId('two-factor-enable').click();
    await page.getByPlaceholder('Password').fill('password');
    await page.getByTestId('confirm-password-submit').click();
    await expect(page.getByText('You have enabled two factor authentication.')).toBeVisible();

    const secretResponse = await page.request.get('/user/two-factor-secret-key');
    expect(secretResponse.ok()).toBeTruthy();
    const { secretKey } = await secretResponse.json();

    await logout(page);

    await page.goto('/login');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill('password');
    await page.getByTestId('login-submit').click();
    await expect(page).toHaveURL(/\/two-factor-challenge/);

    await page.locator('#code').fill(totpCode(secretKey));
    await page.getByTestId('two-factor-challenge-submit').click();
    await expect(page).toHaveURL(/\/dashboard/);

    await page.goto('/user/profile');
    await page.getByTestId('two-factor-disable').click();
    await page.getByPlaceholder('Password').fill('password');
    await page.getByTestId('confirm-password-submit').click();
    await expect(page.getByText('You have not enabled two factor authentication.')).toBeVisible();
  });

  test('delete account', async ({ page }) => {
    const email = `e2e-delete-${Date.now()}@example.com`;
    await page.goto('/register');
    await page.locator('#name').fill('Delete Me');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill('password');
    await page.locator('#password_confirmation').fill('password');
    await page.locator('#registration_secret').fill(SEED.registrationSecret);
    await page.getByTestId('register-submit').click();
    await expect(page).toHaveURL(/\/email\/verify/);

    await page.goto(verificationUrl(email));

    await page.goto('/user/profile');
    await page.getByRole('button', { name: 'Delete Account' }).first().click();
    await page.getByPlaceholder('Password').fill('password');
    await page.getByRole('button', { name: 'Delete Account' }).last().click();
    await expect(page).toHaveURL('/');

    await page.goto('/login');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill('password');
    await page.getByTestId('login-submit').click();
    await expect(page.getByText(/credentials do not match/i)).toBeVisible();
  });
});
