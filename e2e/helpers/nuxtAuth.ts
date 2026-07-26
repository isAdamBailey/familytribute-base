import type { Page } from '@playwright/test';
import { SEED } from '../constants';

/**
 * A real Nuxt login page exists now (issue #19 Phase 5, see
 * e2e/tests/nuxt-auth.spec.ts), but dashboard.spec.ts/account.spec.ts don't
 * care about the login UI itself — they just need a session. This establishes
 * one the same way the Sanctum SPA flow works from any client: prime the CSRF
 * cookie, then POST credentials with the resulting XSRF header. `page.request`
 * shares the page's browser-context cookie jar, so the session cookie set
 * here is sent automatically once `page.goto()` navigates to the Nuxt origin
 * (both origins share the `localhost` cookie domain in local/CI runs).
 */
export function backendBaseURL(): string {
  return process.env.E2E_BACKEND_BASE_URL ?? 'http://localhost:8000';
}

/**
 * `page.goto()` resolves once the `load` event fires, but Nuxt's client-side
 * hydration (attaching Vue reactivity to the server-rendered HTML) finishes
 * slightly later. A `.fill()` that races ahead of hydration touches the raw
 * DOM value; when hydration then mounts, Vue reconciles the input back to
 * its own (still server-initial) state, silently discarding the fill. This
 * only bites forms whose fields start pre-populated from server data (e.g.
 * SiteSettingsForm's title, reloaded after a prior update) — a form starting
 * empty looks unaffected either way. Use this instead of a bare `page.goto`
 * whenever the next step fills a field seeded from server-rendered state.
 *
 * `waitUntil: 'networkidle'` was tried first but is the wrong signal here —
 * it waits for ALL network activity (including the Google Fonts / picsum.photos
 * requests these pages make) to go quiet, which is unrelated to hydration and
 * can stall well past when Vue has actually mounted. A short bounded wait
 * after `load` is a more direct proxy for "hydration has had time to run".
 */
export async function gotoHydrated(page: Page, url: string) {
  await page.goto(url, { waitUntil: 'load' });
  await page.waitForTimeout(500);
}

/** Primes the CSRF cookie and returns the header needed to POST to a Fortify route with it. */
async function csrfHeaders(page: Page, base: string): Promise<Record<string, string>> {
  await page.request.get(`${base}/sanctum/csrf-cookie`);

  const cookies = await page.context().cookies();
  const token = decodeURIComponent(cookies.find(c => c.name === 'XSRF-TOKEN')?.value ?? '');

  return { Accept: 'application/json', 'X-XSRF-TOKEN': token };
}

export async function loginViaApi(
  page: Page,
  user: { email: string; password: string } = SEED.users.admin,
) {
  const base = backendBaseURL();

  const response = await page.request.post(`${base}/login`, {
    headers: await csrfHeaders(page, base),
    data: { email: user.email, password: user.password },
  });

  if (!response.ok()) {
    throw new Error(`Nuxt e2e login failed: ${response.status()} ${await response.text()}`);
  }
}

export async function registerViaApi(
  page: Page,
  user: { name: string; email: string; password: string },
) {
  const base = backendBaseURL();

  const response = await page.request.post(`${base}/register`, {
    headers: await csrfHeaders(page, base),
    data: {
      name: user.name,
      email: user.email,
      password: user.password,
      password_confirmation: user.password,
      registration_secret: SEED.registrationSecret,
    },
  });

  if (!response.ok()) {
    throw new Error(`Nuxt e2e register failed: ${response.status()} ${await response.text()}`);
  }
}
