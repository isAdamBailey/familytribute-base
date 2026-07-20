# Playwright e2e (Phase 0)

Specs live in `e2e/tests` and encode the post–Nuxt-cutover product surface. At cutover, only `E2E_BASE_URL` (and auth/host wiring) should need to change.

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
npx playwright install chromium
npm run build
npm run test:e2e
```

Playwright starts the app with `e2e/scripts/start-app.sh` (sqlite + `migrate:fresh --seed` + `artisan serve`) unless `E2E_BASE_URL` is set.

Against Sail (Docker running, already seeded):

```bash
E2E_BASE_URL=http://localhost E2E_ARTISAN="./vendor/bin/sail artisan" npm run test:e2e
```

Use local disk for uploads: leave `AWS_BUCKET` empty so the `s3` disk falls back to `storage/app/public`.

## Helpers

- `php artisan e2e:signed-url verification {email}`
- `php artisan e2e:signed-url password-reset {email}`

Available when `E2E_HELPERS=true` or `APP_ENV` is `local`/`testing`.

## Running against the Nuxt frontend (issue #19 migration)

The suite is `baseURL`-driven, so the same specs can run against either the
Inertia app or the new Nuxt frontend in `frontend/` (see `frontend/README.md`).

The `default` run (above) is the **release gate** and always targets the
Inertia app. A separate **non-gating smoke** targets Nuxt for the pages that
have been migrated (Phase 2: Home only):

```bash
# Boots seeded Laravel (:8000) + built Nuxt (:3000), runs the Nuxt-covered specs
bash e2e/scripts/nuxt-smoke.sh
```

Override coverage as pages migrate: `GREP="home|people" SPEC_FILTER=public.spec.ts bash e2e/scripts/nuxt-smoke.sh`.

To point the suite at an already-running Nuxt server manually:

```bash
# Nuxt on :3000, seeded Laravel API on :8000
E2E_BASE_URL=http://localhost:3000 npm run test:e2e -- public.spec.ts --grep "home"
```

In CI this runs as the `Nuxt e2e smoke (non-gating)` job. It uses
`continue-on-error`, so a red Nuxt smoke never blocks a merge while the rewrite
is incomplete — the Inertia `Playwright e2e` job stays the required check until
cutover (Phase 6).
