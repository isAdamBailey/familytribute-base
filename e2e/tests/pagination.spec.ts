import { test, expect } from '@playwright/test';
import { cleanupPeople, ensurePeople } from '../helpers/artisan';
import { backendBaseURL } from '../helpers/nuxtAuth';

const MARKER = 'E2EPaginate';

// People/Pictures/Stories share useListPage + infinite scroll. One list page
// covers the pagination bug (IntersectionObserver never attached because
// onMounted ran after await in the async composable).

test.describe('List pagination', () => {
  test.afterEach(() => {
    cleanupPeople();
  });

  test('people index loads the next page on scroll', async ({ page }) => {
    // Default Laravel page size is 15 — need a second page of marked fixtures.
    ensurePeople(20);

    const api = backendBaseURL();
    const page1 = await page.request.get(`${api}/api/people?search=${MARKER}&page=1`);
    expect(page1.ok()).toBeTruthy();
    const page1Body = await page1.json();
    expect(page1Body.people.data).toHaveLength(15);
    expect(page1Body.people.links.next).toBeTruthy();

    const page2 = await page.request.get(`${api}/api/people?search=${MARKER}&page=2`);
    expect(page2.ok()).toBeTruthy();
    const page2Body = await page2.json();
    expect(page2Body.people.data.length).toBeGreaterThan(0);

    const page1Names = new Set<string>(page1Body.people.data.map((p: { full_name: string }) => p.full_name));
    const page2OnlyName = page2Body.people.data
      .map((p: { full_name: string }) => p.full_name)
      .find((name: string) => !page1Names.has(name));
    expect(page2OnlyName).toBeTruthy();

    const page2Requests: string[] = [];
    page.on('request', (req) => {
      if (req.url().includes('/api/people') && req.url().includes('page=2')) {
        page2Requests.push(req.url());
      }
    });

    await page.goto(`/people?search=${MARKER}`);
    await expect(page.getByRole('heading', { name: 'People' })).toBeVisible();

    const cards = page.locator('main p.font-bold');
    await expect(cards).toHaveCount(15);
    await expect(page.getByTestId('people-infinite-scroll-sentinel')).toBeVisible();

    await page.getByTestId('people-infinite-scroll-sentinel').scrollIntoViewIfNeeded();

    await expect.poll(() => page2Requests.length).toBeGreaterThan(0);
    await expect(cards).toHaveCount(page1Body.people.meta.total);
    await expect(page.getByText(page2OnlyName!, { exact: true })).toBeVisible();
    await expect(page.getByTestId('people-infinite-scroll-sentinel')).toHaveCount(0);
  });
});
