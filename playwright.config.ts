delete process.env.NO_COLOR;

import { defineConfig, devices } from '@playwright/test';

// Fixed ports, matching .env.e2e.example (APP_URL/FRONTEND_URLS/
// SANCTUM_STATEFUL_DOMAINS) and e2e/scripts/start-api.sh|start-nuxt.sh —
// nothing in this repo overrides them.
const baseURL = process.env.E2E_BASE_URL ?? 'http://localhost:3000';

export default defineConfig({
  testDir: './e2e/tests',
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
  // Boots the seeded Laravel API and the built Nuxt frontend as two
  // independent servers — Nuxt is the only frontend since the issue #19
  // cutover, so both are needed for every spec, not just a subset.
  webServer: process.env.E2E_BASE_URL
    ? undefined
    : [
        {
          command: 'bash e2e/scripts/start-api.sh',
          url: 'http://127.0.0.1:8000/api/home',
          reuseExistingServer: !process.env.CI,
          timeout: 180_000,
        },
        {
          command: 'bash e2e/scripts/start-nuxt.sh',
          url: baseURL,
          reuseExistingServer: !process.env.CI,
          timeout: 180_000,
        },
      ],
});
