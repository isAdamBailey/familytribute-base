import { expect, type Page } from '@playwright/test';
import { SEED } from '../constants';

export async function loginAs(
  page: Page,
  user: { email: string; password: string } = SEED.users.admin,
) {
  await page.goto('/login');
  await page.locator('#email').fill(user.email);
  await page.locator('#password').fill(user.password);
  await page.getByTestId('login-submit').click();
  await expect(page).toHaveURL(/\/dashboard/);
  await expect(page.getByText(/Welcome to .*dashboard/i)).toBeVisible();
}

export async function openUserMenu(page: Page) {
  const trigger = page.locator('nav button').filter({ has: page.locator('img') });
  await expect(trigger).toBeVisible();
  await trigger.click();
  await expect(page.locator('nav').getByRole('button', { name: 'Log Out' }).first()).toBeVisible();
}

export async function logout(page: Page) {
  const dismiss = page.getByRole('button', { name: 'Dismiss' });
  if (await dismiss.isVisible().catch(() => false)) {
    await dismiss.click();
  }

  await openUserMenu(page);
  await page.locator('nav').getByRole('button', { name: 'Log Out' }).first().click();
  await expect(page).toHaveURL('/');
  await expect(page.getByRole('link', { name: 'Log In' })).toBeVisible();
}

export async function fillWysiwyg(page: Page, testId: string, text: string) {
  const editor = page.getByTestId(testId).locator('.ProseMirror');
  await editor.click();
  await page.keyboard.press(process.platform === 'darwin' ? 'Meta+A' : 'Control+A');
  await page.keyboard.press('Backspace');
  await editor.pressSequentially(text, { delay: 10 });
  await expect(editor).toContainText(text);
}

export async function fillDate(page: Page, wrapperTestId: string, isoDate: string) {
  const input = page.getByTestId(wrapperTestId).locator('input').first();
  await input.click();
  await input.fill(isoDate);
  await input.press('Tab');
  await expect(input).toHaveValue(isoDate);
}

export async function expectFlash(page: Page, text: string) {
  const banner = page.getByTestId('flash-banner');
  await expect(banner).toBeVisible();
  await expect(banner).toHaveText(text);
}
