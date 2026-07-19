import { test, expect } from '@playwright/test';
import { SEED } from '../constants';
import { expectSeo } from '../helpers/seo';

test.describe('Public browsing', () => {
  test('home shows site title and CTAs', async ({ page }) => {
    await page.goto('/');
    await expect(page.getByRole('heading', { name: SEED.siteTitle })).toBeVisible();
    await expect(page.getByRole('link', { name: /Meet the Family/i })).toBeVisible();
    await expect(page.getByRole('link', { name: /View Pictures/i })).toBeVisible();
    await expect(page.getByRole('navigation').getByRole('link', { name: 'People', exact: true })).toBeVisible();
    await expect(page.getByRole('navigation').getByRole('link', { name: 'Pictures', exact: true })).toBeVisible();
    await expect(page.getByRole('navigation').getByRole('link', { name: 'Stories', exact: true })).toBeVisible();
  });

  test('people index and person show', async ({ page }) => {
    await page.goto('/people');
    await expect(page.getByRole('heading', { name: 'People' })).toBeVisible();
    await expect(page.getByRole('link', { name: SEED.people.ada.name })).toBeVisible();

    await page.getByRole('link', { name: SEED.people.ada.name }).click();
    await expect(page).toHaveURL(SEED.people.ada.path);
    await expect(page.getByRole('heading', { name: SEED.people.ada.name })).toBeVisible();
    await expect(page.getByText(/Analytical Engine/i)).toBeVisible();
  });

  test('pictures index and public picture show', async ({ page }) => {
    await page.goto('/pictures');
    await expect(page.getByRole('heading', { name: 'Pictures' })).toBeVisible();
    await expect(page.getByText(SEED.pictures.public.title)).toBeVisible();
    await expect(page.getByText(SEED.pictures.private.title)).toHaveCount(0);

    await page.getByRole('link', { name: SEED.pictures.public.title }).click();
    await expect(page).toHaveURL(SEED.pictures.public.path);
    await expect(page.getByRole('heading', { name: SEED.pictures.public.title })).toBeVisible();
  });

  test('stories index and public story show', async ({ page }) => {
    await page.goto('/stories');
    await expect(page.getByRole('heading', { name: 'Stories' })).toBeVisible();
    await expect(page.getByText(SEED.stories.public.title)).toBeVisible();
    await expect(page.getByText(SEED.stories.private.title)).toHaveCount(0);

    await page.getByRole('link', { name: SEED.stories.public.title }).click();
    await expect(page).toHaveURL(SEED.stories.public.path);
    await expect(page.getByRole('heading', { name: SEED.stories.public.title })).toBeVisible();
  });

  test('404 fallback', async ({ page }) => {
    await page.goto('/this-page-does-not-exist-e2e');
    await expect(page.getByText('404: Page Not Found')).toBeVisible();
    await expect(page.getByRole('link', { name: /Back Home/i })).toBeVisible();
  });
});

test.describe('Public SEO', () => {
  test('home SEO tags', async ({ page }) => {
    await page.goto('/');
    await expectSeo(page, {
      titleIncludes: [/Home/i, SEED.siteTitle],
      descriptionIncludes: /history of our family/i,
    });
  });

  test('person SEO tags', async ({ page }) => {
    await page.goto(SEED.people.ada.path);
    await expectSeo(page, {
      titleIncludes: [SEED.people.ada.name],
      descriptionIncludes: /mathematician/i,
    });
  });

  test('picture SEO tags', async ({ page }) => {
    await page.goto(SEED.pictures.public.path);
    await expectSeo(page, {
      titleIncludes: [SEED.pictures.public.title],
      descriptionIncludes: /picnic/i,
    });
  });

  test('story SEO tags', async ({ page }) => {
    await page.goto(SEED.stories.public.path);
    await expectSeo(page, {
      titleIncludes: [SEED.stories.public.title],
      descriptionIncludes: /garden/i,
    });
  });
});
