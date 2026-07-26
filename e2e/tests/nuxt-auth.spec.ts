import { test, expect } from '@playwright/test';
import { SEED } from '../constants';
import { loginAs, logout } from '../helpers/auth';
import { passwordResetUrl, verificationUrl } from '../helpers/artisan';
import { gotoHydrated, registerViaApi } from '../helpers/nuxtAuth';

// Auth flow coverage (issue #19, Phase 5): login/logout, register+verify,
// forgot/reset-password against the real Nuxt pages. Any navigation this file
// does with a bare `page.goto()` immediately followed by filling in a field
// (rather than through the shared loginAs()/logout() helpers) uses
// `gotoHydrated()` instead — a Vue SSR page's hydration can otherwise
// reconcile a filled-in-early field back to its pre-hydration (usually empty)
// value, silently discarding it.
test.describe('Nuxt auth', () => {
  test('login and logout', async ({ page }) => {
    await loginAs(page);
    await expect(page.getByText(/Welcome to .*dashboard/i)).toBeVisible();
    await logout(page);
    await expect(page.getByRole('link', { name: 'Log In' })).toBeVisible();
  });

  test('register, verify email, reach dashboard', async ({ page }) => {
    const email = `e2e-nuxt-register-${Date.now()}@example.com`;

    await gotoHydrated(page, '/register');
    await page.locator('#name').fill('E2E Nuxt Registrant');
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
    const email = `e2e-nuxt-reset-${Date.now()}@example.com`;

    // The register/verify UI flow itself is already covered by the test
    // above — this test only cares about forgot/reset password, so set up
    // the account via the API shortcut instead of re-driving that same UI.
    await registerViaApi(page, { name: 'E2E Nuxt Reset User', email, password: 'password' });
    await gotoHydrated(page, '/');
    await logout(page);

    await gotoHydrated(page, '/forgot-password');
    await page.locator('#email').fill(email);
    await page.getByTestId('forgot-password-submit').click();
    await expect(page.getByText(/we have emailed/i)).toBeVisible();

    await gotoHydrated(page, passwordResetUrl(email));
    await page.locator('#password').fill('new-password');
    await page.locator('#password_confirmation').fill('new-password');
    await page.getByTestId('reset-password-submit').click();
    // Already on /login via the reset-password page's own client-side
    // navigateTo() — no page.goto() needed (or wanted: that would force a
    // fresh SSR render and reintroduce the hydration race noted above).
    await expect(page).toHaveURL(/\/login/);
    await page.locator('#email').fill(email);
    await page.locator('#password').fill('new-password');
    await page.getByTestId('login-submit').click();
    // registerViaApi() doesn't verify the account (that flow is covered by
    // the test above), so a successful login lands on /email/verify rather
    // than /dashboard — this only needs to confirm the new password works.
    await expect(page).not.toHaveURL(/\/login/);
  });
});
