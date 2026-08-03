# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## About

FamilyTribute is a Laravel JSON API + Nuxt 4 (Vue 3, TypeScript, SSR) application for families to share their history — people (with obituaries), pictures, and stories — publicly or privately.

## Development Commands

All PHP/server commands run inside the Sail Docker environment via `sail`:

```bash
sail up                              # Start Docker environment
sail artisan migrate:fresh --seed   # Reset DB with seed data
sail artisan migrate                # Run pending migrations
sail artisan tinker                 # REPL
```

The Nuxt frontend lives in `frontend/` and runs independently of Sail:

```bash
cd frontend
npm run dev                          # Nuxt dev server (HMR) on :3000
npm run build                        # Production build (.output/)
npm run typecheck                    # vue-tsc type check
npm run lint                         # ESLint
```

### Testing

PHPUnit Feature tests use SQLite in-memory (configured in `phpunit.xml`) — no Unit suite. Playwright e2e (root `e2e/` dir) drives the real Nuxt UI against a seeded Laravel API; see `e2e/README.md`.

```bash
sail test                                            # Run all PHPUnit tests
sail test --filter PersonTest                        # Run a single test class
sail test tests/Feature/Api/StoriesApiTest.php        # Run a single file
npm run test:e2e                                     # Playwright e2e (boots both servers itself)
```

### Code Style

```bash
sail pint                            # PHP code style fixer (Laravel Pint)
```

Seed credentials: `test@test.com` / `test2@test.com`, password `password` (see `database/seeders/SiteSeeder.php`).

## Architecture

### Stack

- **Backend**: Laravel 13, PHP 8.3 — a JSON API only, no server-rendered pages. Everything it answers on lives under `/api/*` plus `/sanctum/csrf-cookie`.
- **Frontend**: Nuxt 4 (Vue 3 + TypeScript), in `frontend/`, deployed separately from the Laravel API. SSR is enabled so public pages serve real server-rendered OG/Twitter meta to crawlers.
- **Styling**: Tailwind CSS v3, configured independently in `frontend/tailwind.config.ts`.
- **Auth**: Laravel Fortify (login/register/password reset/email verification/2FA), registered entirely under the `/api` prefix (`config/fortify.php`) so it shares an origin with the JSON API. Sanctum SPA cookie auth — `useAuth()`/`useAccount()` in `frontend/app/composables` wrap the CSRF-cookie + session flow. There is no team/role model: every verified user has equal (editor-level) access, matching the single-family design.
- **Storage**: AWS S3 for pictures and story media uploads.
- **Slugs**: `spatie/laravel-sluggable` — all public models use slug-based routing.

### Data Model

The core entities and their relationships:

- **Person** — the central entity. Has a slug from `first_name + last_name`. Has one `Obituary`, belongs-to-many `Picture` and `Story`, and a self-referential many-to-many for `parents`/`children` (via `parent_child` pivot).
- **Obituary** — belongs to one `Person`; holds `birth_date`, `death_date`, rich-text `content`, and an optional `background_photo_url`.
- **Picture** — belongs-to-many `Person` via `person_picture` pivot. Has `private` flag.
- **Story** — belongs-to-many `Person` via `person_story` pivot. Has `private` flag, a `year`, rich-text `content`, and an optional `media_path` (audio/video file on S3).
- **SiteSetting** — singleton model (always use `SiteSetting::first()`). Holds site `title`, `description`, and `registration` toggle. Shared to Nuxt via `GET /api/site-settings`, which also carries `google_site_tag` (the GA4 ID from `GOOGLE_SITE_TAG`, production-only) — Nuxt runs as a separate daemon that can't read Laravel's `.env`, so site-level env config reaches the frontend on this endpoint. It's the one request every page already makes.

### Privacy

`private` content (pictures, stories) is hidden from unauthenticated users via query scopes on the relationships (list endpoints) and an `abort(404)` on direct access (show endpoints). Always check `when(!auth()->user(), ...)` patterns when adding new queries on these models.

### Backend routing

- `routes/api.php` — the entire JSON API, registered under the `api` middleware group (Sanctum's `EnsureFrontendRequestsAreStateful` + `throttle:api`). Controllers live in `app/Http/Controllers/Api/*` and return `app/Http/Resources/*` — never pass raw Eloquent models to a response.
- Fortify's own auth routes are registered separately by its service provider (`config('fortify.prefix')` = `api`), using the `web` middleware group (session/cookie/CSRF) — not `routes/api.php`'s `api` group.
- There are no other routes. A 401/403/404 from this app is always a plain JSON response, not a redirect or rendered page — `App\Http\Middleware\EnsureEmailIsVerified` and `App\Http\Middleware\Authenticate` are JSON-only overrides of Laravel's defaults for exactly this reason.

### Nuxt frontend

- Pages live in `frontend/app/pages/`, components in `frontend/app/components/` (feature subfolders: `dashboard/`, `modals/`, `profile/`), composables in `frontend/app/composables/`.
- `frontend/app/plugins/api.ts` provides `$api` — a `$fetch` instance wired for the Sanctum SPA cookie contract (credentials, CSRF header, SSR cookie-forwarding). Use `useApiFetch()` for page-level SSR reads, `$api` directly for imperative calls.
- `useAuth()` handles login/register/logout/password-reset/email-verification/2FA-challenge against Fortify's `/api/*` endpoints. `useAccount()` handles authenticated profile/password/2FA/sessions/account-deletion.
- SEO is owned entirely by Nuxt via `useSeoMeta()`/`useHead()` per page, sourced from API resource data — there is no PHP-side SEO code.
- Rich text editing uses Tiptap (`WysiwygEditor.client.vue`).

### File Storage

Photos and story media are stored on S3. Model accessors on `photo_url`, `url`, `background_photo_url`, and `media_url` automatically resolve stored paths to full S3 URLs via `Storage::url()`. URLs already starting with `https://` are returned as-is (legacy/external URLs).

## Deployment

See `DEPLOY.md` for the production Forge topology (single origin: nginx routes `/api` + `/sanctum` to PHP-FPM, everything else to the Nuxt Node process) and exact settings.

## Design Context

Full specs live in `PRODUCT.md` (strategic) and `DESIGN.md` (visual). Quick reference:

- **Register:** Brand — the public tribute experience is the product. Design should celebrate lives, not serve a workflow.
- **North Star:** "The Living Family Album" — warm, intimate, handmade, alive.
- **Palette:** Hearthlight amber (`#bf8028`). Any `indigo-*`, `violet-*`, or `sky-*` Tailwind class on a UI surface is a regression to be replaced.
- **Typography:** Gwendolyn (script, bold) for display/headlines. Open Sans for body. No uppercase-tracking on buttons or labels.
- **Shadows:** Amber-tinted (`rgba(140, 80, 30, 0.10)` ambient). Never cool-gray.
- **Anti-references:** Legacy.com (clinical), grief-heavy dark memorials, generic SaaS look (eyebrow labels, tracked uppercase CTAs, identical card grids, gradient text).
