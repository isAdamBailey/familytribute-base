import { test, expect } from '@playwright/test';
import { SEED } from '../constants';
import { loginAs } from '../helpers/auth';

test.describe('Privacy', () => {
  test('private picture and story are hidden from guests', async ({ page }) => {
    await page.goto('/pictures');
    await expect(page.getByText(SEED.pictures.private.title)).toHaveCount(0);

    // Private content 404s at the API layer; Nuxt renders its 404 error page
    // in place (no redirect) rather than bouncing back to the index.
    await page.goto(SEED.pictures.private.path);
    await expect(page.getByText('404: Page Not Found')).toBeVisible();

    await page.goto('/stories');
    await expect(page.getByText(SEED.stories.private.title)).toHaveCount(0);

    await page.goto(SEED.stories.private.path);
    await expect(page.getByText('404: Page Not Found')).toBeVisible();

    await page.goto(SEED.people.ada.path);
    await expect(page.getByText(SEED.pictures.private.title)).toHaveCount(0);
    await expect(page.getByText(SEED.stories.private.title)).toHaveCount(0);
  });

  test('private picture and story are visible when logged in', async ({ page }) => {
    await loginAs(page);

    await page.goto('/pictures');
    await expect(page.getByText(SEED.pictures.private.title)).toBeVisible();

    await page.goto(SEED.pictures.private.path);
    await expect(page).toHaveURL(SEED.pictures.private.path);
    await expect(page.getByRole('heading', { name: SEED.pictures.private.title })).toBeVisible();

    await page.goto('/stories');
    await expect(page.getByText(SEED.stories.private.title)).toBeVisible();

    await page.goto(SEED.stories.private.path);
    await expect(page).toHaveURL(SEED.stories.private.path);
    await expect(page.getByRole('heading', { name: SEED.stories.private.title })).toBeVisible();
    await expect(page.getByText('Private', { exact: true })).toBeVisible();

    await page.goto(SEED.people.ada.path);
    await expect(page.getByText(SEED.pictures.private.title)).toBeVisible();
    await expect(page.getByText(SEED.stories.private.title)).toBeVisible();
  });
});
