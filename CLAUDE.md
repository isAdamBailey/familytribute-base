# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## About

FamilyTribute is a Laravel + Inertia.js + Vue 3 application for families to share their history — people (with obituaries), pictures, and stories — publicly or privately.

## Development Commands

All PHP/server commands run inside the Sail Docker environment via `sail`:

```bash
sail up                              # Start Docker environment
sail artisan migrate:fresh --seed   # Reset DB with seed data
sail artisan migrate                # Run pending migrations
sail artisan tinker                 # REPL

npm run dev                          # Start Vite dev server (HMR)
npm run build                        # Production asset build
npm run lint                         # ESLint fix (JS/Vue)
npm run format                       # Prettier format
```

### Testing

Tests use SQLite in-memory (configured in `phpunit.xml`). Only Feature tests exist — no Unit suite.

```bash
sail test                                            # Run all tests
sail test --filter PersonTest                        # Run a single test class
sail test tests/Feature/StoriesTest.php              # Run a single file
npm run test:e2e                                     # Playwright e2e (see e2e/README.md)
```

### Code Style

```bash
sail pint                            # PHP code style fixer (Laravel Pint)
```

Seed credentials: `test@test.com` / `test2@test.com` (passwords from factory defaults).

## Architecture

### Stack

- **Backend**: Laravel 13, PHP 8.3
- **Frontend**: Vue 3 + Inertia.js v2 (SPA routing without a separate API)
- **Styling**: Tailwind CSS v3
- **Auth/Teams**: Laravel Jetstream (team-based, Sanctum sessions)
- **Storage**: AWS S3 for pictures and story media uploads
- **Slugs**: `spatie/laravel-sluggable` — all public models use slug-based routing

### Data Model

The core entities and their relationships:

- **Person** — the central entity. Has a slug from `first_name + last_name`. Has one `Obituary`, belongs-to-many `Picture` and `Story`, and a self-referential many-to-many for `parents`/`children` (via `parent_child` pivot).
- **Obituary** — belongs to one `Person`; holds `birth_date`, `death_date`, rich-text `content`, and an optional `background_photo_url`.
- **Picture** — belongs-to-many `Person` via `person_picture` pivot. Has `private` flag.
- **Story** — belongs-to-many `Person` via `person_story` pivot. Has `private` flag, a `year`, rich-text `content`, and an optional `media_path` (audio/video file on S3).
- **SiteSetting** — singleton model (always use `SiteSetting::first()`). Holds site `title`, `description`, and `registration` toggle. Shared to all Inertia pages via `HandleInertiaRequests`.

### Privacy

`private` content (pictures, stories) is hidden from unauthenticated users via query scopes on the relationships. Always check `when(!auth()->user(), ...)` patterns when adding new queries on these models.

### Teams & Auth

Jetstream teams are used as the access model, but the app is designed for a single family — there's only ever **one team**. The `EnsureHasTeam` middleware auto-joins any authenticated user to that team as `editor`. Authenticated routes require the `auth:sanctum`, `verified`, and `team` middleware stack.

### Inertia / Frontend

- Pages live in `resources/js/Pages/` and are resolved by name in `app.js`.
- Shared global props (settings, meta tags) are injected in `HandleInertiaRequests::share()`.
- `Base/` holds primitive UI components globally registered (`BaseButton`, `ScrollTop`, `Link`).
- `Components/` holds feature-specific components (grids, containers).
- `Layouts/` provides `AppLayout` and `HomeLayout`.
- Rich text editing uses Tiptap (`Wysiwyg.vue`).

### SEO

Controllers that render public pages use the `HasSeoTags` trait to set Open Graph and Twitter Card meta. Call `setTitleStart()`, `setDescription()`, `setOgImage()` then `renderSeo()` before the `Inertia::render()` call. Meta is passed to the frontend via the `meta` shared prop.

### API Resources

All data passed to Inertia pages is transformed through `app/Http/Resources/` (e.g., `PersonResource`, `StoryResource`). Don't pass raw Eloquent models directly to `Inertia::render()`.

### File Storage

Photos and story media are stored on S3. Model accessors on `photo_url`, `url`, `background_photo_url`, and `media_url` automatically resolve stored paths to full S3 URLs via `Storage::url()`. URLs already starting with `https://` are returned as-is (legacy/external URLs).

## Design Context

Full specs live in `PRODUCT.md` (strategic) and `DESIGN.md` (visual). Quick reference:

- **Register:** Brand — the public tribute experience is the product. Design should celebrate lives, not serve a workflow.
- **North Star:** "The Living Family Album" — warm, intimate, handmade, alive.
- **Palette:** Transitioning from indigo (current codebase) to **Hearthlight amber** (`#bf8028`). Any `indigo-*`, `violet-*`, or `sky-*` Tailwind class on a UI surface is a regression to be replaced.
- **Typography:** Gwendolyn (script, bold) for display/headlines. Open Sans for body. No uppercase-tracking on buttons or labels.
- **Shadows:** Amber-tinted (`rgba(140, 80, 30, 0.10)` ambient). Never cool-gray.
- **Anti-references:** Legacy.com (clinical), grief-heavy dark memorials, generic SaaS look (eyebrow labels, tracked uppercase CTAs, identical card grids, gradient text).
