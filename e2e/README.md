# Playwright e2e

Specs live in `e2e/tests` and run against the Nuxt frontend (`frontend/`) backed by the Laravel JSON API — the only frontend since the issue #19 cutover.

## Seed contract

`php artisan migrate:fresh --seed` (via `SiteSeeder`) must provide:

| Key | Value |
|-----|-------|
| Admin | `test@test.com` / `password` |
| Editor | `test2@test.com` / `password` |
| Site title | `Family Tribute` |
| Registration secret | `familytribute` |
| People | `/ada-lovelace`, `/alan-turing` |
| Pictures | `/pictures/public-picnic` (public), `/pictures/private-portrait` (private) |
| Stories | `/stories/public-garden-story` (public), `/stories/private-letter` (private) |

Keep this in sync with `e2e/constants.ts`.

## Local run

```bash
cp .env.e2e.example .env.e2e   # first time
npm ci
npm --prefix frontend ci
npx playwright install chromium
npm run test:e2e
```

`npm run test:e2e` first builds the frontend (`build:frontend`, skipped if `E2E_BASE_URL` is set), then runs Playwright, which boots two servers via its `webServer` config: `e2e/scripts/start-api.sh` (seeded Laravel API, sqlite + `migrate:fresh --seed` + `artisan serve` on :8000) and `e2e/scripts/start-nuxt.sh` (the already-built Nuxt frontend on :3000, via `frontend/deploy/start-nuxt.sh`). Both are skipped if `E2E_BASE_URL` is already set, so you can point the suite at servers you're already running:

```bash
# Nuxt dev server on :3000, seeded Laravel API on :8000
E2E_BASE_URL=http://localhost:3000 E2E_BACKEND_BASE_URL=http://localhost:8000 npm run test:e2e
```

Against Sail (Docker running, already seeded) for the backend:

```bash
E2E_BACKEND_BASE_URL=http://localhost E2E_ARTISAN="./vendor/bin/sail artisan" npm run test:e2e
```

Use local disk for uploads: leave `AWS_BUCKET` empty so the `s3` disk falls back to `storage/app/public`.

## Helpers

- `php artisan e2e:signed-url verification {email}`
- `php artisan e2e:signed-url password-reset {email}`

Available when `E2E_HELPERS=true` or `APP_ENV` is `local`/`testing`.
