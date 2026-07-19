import { test, expect } from '@playwright/test';
import { SEED } from '../constants';
import { loginAs, logout } from '../helpers/auth';
import { passwordResetUrl, verificationUrl } from '../helpers/artisan';

test.describe('Auth', () => {
  test('login and logout', async ({ page }) => {
    await loginAs(page);
    await expect(page.getByText(/Welcome to .*dashboard/i)).toBeVisible();
    await logout(page);
    await expect(page.getByRole('link', { name: 'Log In' })).toBeVisible();
  });

  test('register, verify email, reach dashboard', async ({ page }) => {
    const email = `e2e-register-${Date.now()}@example.com`;

    await page.goto('/register');
    await page.locator('#name').fill('E2E Registrant');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill('password');
    await page.locator('#password_confirmation').fill('password');
    await page.locator('#registration_secret').fill(SEED.registrationSecret);
    await page.getByTestId('register-submit').click();

    await expect(page).toHaveURL(/\/email\/verify/);

    const url = verificationUrl(email);
    await page.goto(url);
    await expect(page).toHaveURL(/\/dashboard/);
  });

  test('forgot and reset password', async ({ page }) => {
    const email = `e2e-reset-${Date.now()}@example.com`;

    await page.goto('/register');
    await page.locator('#name').fill('E2E Reset User');
    await page.locator('#email').fill(email);
    await page.locator('#password').fill('password');
    await page.locator('#password_confirmation').fill('password');
    await page.locator('#registration_secret').fill(SEED.registrationSecret);
    await page.getByTestId('register-submit').click();
    await page.goto(verificationUrl(email));
    await expect(page).toHaveURL(/\/dashboard/);

    await logout(page);

    await page.goto('/forgot-password');
    await page.locator('#email').fill(email);
    await page.getByTestId('forgot-password-submit').click();
    await expect(page.getByText(/we have emailed/i)).toBeVisible();

    await page.goto(passwordResetUrl(email));
    await page.locator('#password').fill('new-password');
    await page.locator('#password_confirmation').fill('new-password');
    await page.getByTestId('reset-password-submit').click();

    await loginAs(page, { email, password: 'new-password' });
  });
});
