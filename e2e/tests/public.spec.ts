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

// Nuxt renders its app into a `<div id="__nuxt">` root; the still-live
// Inertia app has no such element. Used to gate assertions on new
// Nuxt-only functionality without hard-coding which origin is under test.
async function skipUnlessNuxt(page: import('@playwright/test').Page, reason: string) {
  test.skip(await page.locator('#__nuxt').count() === 0, reason);
}

test.describe('Public SEO', () => {
  // Per-page SEO is asserted on Nuxt (`useSeoMeta`). Inertia still uses
  // `HasSeoTags` until the Phase 6 cutover; skip when Nuxt is not serving.
  const seoReason = 'SEO assertions target Nuxt useSeoMeta; skip when Inertia is still serving';

  test('home SEO tags', async ({ page }) => {
    await page.goto('/');
    await skipUnlessNuxt(page, seoReason);
    await expectSeo(page, {
      titleIncludes: [/Home/i, SEED.siteTitle],
      descriptionIncludes: /history of our family/i,
    });
  });

  test('person SEO tags', async ({ page }) => {
    await page.goto(SEED.people.ada.path);
    await skipUnlessNuxt(page, seoReason);
    await expectSeo(page, {
      titleIncludes: [SEED.people.ada.name],
      descriptionIncludes: /mathematician/i,
    });
  });

  test('picture SEO tags', async ({ page }) => {
    await page.goto(SEED.pictures.public.path);
    await skipUnlessNuxt(page, seoReason);
    await expectSeo(page, {
      titleIncludes: [SEED.pictures.public.title],
      descriptionIncludes: /picnic/i,
    });
  });

  test('story SEO tags', async ({ page }) => {
    await page.goto(SEED.stories.public.path);
    await skipUnlessNuxt(page, seoReason);
    await expectSeo(page, {
      titleIncludes: [SEED.stories.public.title],
      descriptionIncludes: /garden/i,
    });
  });
});

test.describe('Public interactions', () => {
  test('people sort control reorders results', async ({ page }) => {
    await page.goto('/people');
    const names = () => page.locator('main p.font-bold').allTextContents();

    // The new Nuxt page renders a native <select data-testid="sort-select">
    // for sorting; the still-live Inertia page has no such element (it uses a
    // dropdown-menu-of-links instead). Gate on the testid for the new
    // functionality specifically, rather than an ARIA role that could
    // coincidentally match something else, or an env var that says nothing
    // about the actual markup under test.
    const sortSelect = page.getByTestId('sort-select');
    test.skip(await sortSelect.count() === 0, 'sort-select is new Nuxt-only functionality; old Inertia page has no equivalent element');

    await sortSelect.selectOption({ label: 'Last Name (A-Z)' });
    await expect(sortSelect).toHaveValue('last_name:asc');
    await expect(async () => {
      const ordered = await names();
      expect(ordered.indexOf(SEED.people.ada.name)).toBeLessThan(ordered.indexOf(SEED.people.alan.name));
    }).toPass();

    await sortSelect.selectOption({ label: 'Last Name (Z-A)' });
    await expect(sortSelect).toHaveValue('last_name:desc');
    await expect(async () => {
      const ordered = await names();
      expect(ordered.indexOf(SEED.people.alan.name)).toBeLessThan(ordered.indexOf(SEED.people.ada.name));
    }).toPass();
  });

  test('footer shows Log In and Register when signed out', async ({ page }) => {
    await page.goto('/');
    const footer = page.locator('footer');
    await expect(footer.getByRole('link', { name: 'Log In' })).toBeVisible();
    await expect(footer.getByRole('link', { name: 'Register' })).toBeVisible();
  });

  test('footer Log In link reaches a working login form', async ({ page }) => {
    await page.goto('/');
    await page.locator('footer').getByRole('link', { name: 'Log In' }).click();
    await expect(page.locator('#email')).toBeVisible();
    await expect(page.locator('#password')).toBeVisible();
  });

  test('footer Register link reaches a working registration form', async ({ page }) => {
    await page.goto('/');
    await page.locator('footer').getByRole('link', { name: 'Register' }).click();
    await expect(page.locator('#name')).toBeVisible();
    await expect(page.locator('#registration_secret')).toBeVisible();
  });
});
