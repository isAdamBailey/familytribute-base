delete process.env.NO_COLOR;

import { defineConfig, devices } from '@playwright/test';

const baseURL = process.env.E2E_BASE_URL ?? 'http://127.0.0.1:8000';

// dashboard.spec.ts / account.spec.ts (issue #19, Phase 4) are Nuxt-only —
// they log in via loginViaApi()/registerViaApi() (no Nuxt login page exists
// yet, see e2e/helpers/nuxtAuth.ts) and target Nuxt-only routes/testids, so
// they fail outright against the Inertia app. This config's default target
// (and CI's gating "Playwright e2e" job, which runs `playwright test` with
// no file filter) is the Inertia app, so these two are excluded by default.
// e2e/scripts/nuxt-smoke.sh sets NUXT_E2E=true and passes them explicitly.
const isNuxtRun = !!process.env.NUXT_E2E;

export default defineConfig({
  testDir: './e2e/tests',
  testIgnore: isNuxtRun ? undefined : ['**/dashboard.spec.ts', '**/account.spec.ts'],
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 1 : 0,
  workers: 1,
  reporter: process.env.CI ? [['github'], ['list']] : 'list',
  timeout: 60_000,
  expect: { timeout: 10_000 },
  use: {
    baseURL,
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',
    actionTimeout: 15_000,
    navigationTimeout: 30_000,
  },
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
  webServer: process.env.E2E_BASE_URL
    ? undefined
    : {
        command: 'bash e2e/scripts/start-app.sh',
        url: baseURL,
        reuseExistingServer: !process.env.CI,
        timeout: 180_000,
      },
});
