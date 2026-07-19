import { expect, type Page } from '@playwright/test';

/** SEO contract that must survive Nuxt cutover (title + description + OG/Twitter). */
export async function expectSeo(
  page: Page,
  {
    titleIncludes,
    descriptionIncludes,
  }: {
    titleIncludes: string | RegExp | Array<string | RegExp>;
    descriptionIncludes?: string | RegExp;
  },
) {
  const includes = Array.isArray(titleIncludes) ? titleIncludes : [titleIncludes];
  const title = await page.title();

  for (const part of includes) {
    if (typeof part === 'string') {
      expect(title).toContain(part);
    } else {
      expect(title).toMatch(part);
    }
  }

  await expect(page.locator('meta[name="description"]')).toHaveAttribute('content', /.+/);

  if (descriptionIncludes) {
    const content = await page.locator('meta[name="description"]').getAttribute('content');
    if (typeof descriptionIncludes === 'string') {
      expect(content).toContain(descriptionIncludes);
    } else {
      expect(content).toMatch(descriptionIncludes);
    }
  }

  // Prefer property/name tags that Nuxt useSeoMeta will keep. Fall back to whatever
  // the current Inertia+meta package emits after client hydration.
  const ogTitle = page.locator('meta[property="og:title"]');
  const ogDescription = page.locator('meta[property="og:description"]');
  const ogUrl = page.locator('meta[property="og:url"]');
  const twitterCard = page.locator('meta[name="twitter:card"]');
  const twitterTitle = page.locator('meta[name="twitter:title"]');

  if ((await ogTitle.count()) > 0) {
    await expect(ogTitle).toHaveAttribute('content', /.+/);
  }
  if ((await ogDescription.count()) > 0) {
    await expect(ogDescription).toHaveAttribute('content', /.+/);
  }
  if ((await ogUrl.count()) > 0) {
    await expect(ogUrl).toHaveAttribute('content', /.+/);
  }
  if ((await twitterCard.count()) > 0) {
    await expect(twitterCard).toHaveAttribute('content', /summary/);
  }
  if ((await twitterTitle.count()) > 0) {
    await expect(twitterTitle).toHaveAttribute('content', /.+/);
  }
}
