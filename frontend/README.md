# FamilyTribute frontend (Nuxt 4)

The Nuxt 4 + TypeScript rewrite of the FamilyTribute frontend (issue #19). It is
a **standalone SPA/SSR app** that talks to the Laravel backend over the JSON API
(`routes/api.php`) and authenticates via Sanctum SPA cookie auth. It replaces
the Vue 3 + Inertia frontend in `../resources/js` at cutover (Phase 6).

Status: **Phase 2 — scaffolding + walking skeleton.** Only the Home page is
migrated so far; `/people`, `/pictures`, `/stories`, auth, and editor flows land
in later phases.

## Stack

- Nuxt 4 (SSR on) + TypeScript
- Tailwind CSS v3 via `@nuxtjs/tailwindcss` — tokens ported from the Inertia
  app's `../tailwind.config.js` / `../DESIGN.md` (Hearthlight amber palette,
  Gwendolyn + Open Sans)
- `@nuxtjs/color-mode` (class-based dark mode), `@nuxtjs/google-fonts`
  (self-hosted), `@vueuse/nuxt`
- Tiptap 2.x editor (`app/components/WysiwygEditor.client.vue`)
- Data fetching via composables (`app/composables/*`) — no Pinia

## Local development

Requires the Laravel backend running and seeded (see the repo root `README.md`).

```bash
cp .env.example .env      # first time
npm install
npm run dev               # Nuxt dev server on http://localhost:3000
```

The dev server proxies data from the API base in `.env`
(`NUXT_PUBLIC_API_BASE`, default `http://localhost/api` — matches `sail up`'s
nginx on `:80`; override to `:8000` if running the backend via
`php artisan serve` instead).

### Backend config required for cross-origin auth

Because Nuxt (`:3000`) and Laravel (`:80` via Sail) are different origins, the
backend must trust the Nuxt origin for Sanctum SPA cookie auth. In the Laravel
`.env`:

```dotenv
FRONTEND_URLS=http://localhost:3000          # CORS allow-origin (credentialed)
SANCTUM_STATEFUL_DOMAINS=localhost:3000       # treat as stateful SPA
SESSION_DOMAIN=localhost                       # share the session cookie
```

Public read pages (Home, and the Phase 3 pages) work without this — SSR fetches
run server-to-server. It is only required for login and authenticated calls.

> Laravel caches config: after changing these, re-run `php artisan config:clear`
> (or `config:cache`) for them to take effect.

## Scripts

| Command | Description |
|---------|-------------|
| `npm run dev` | Dev server (HMR) on `:3000` |
| `npm run build` | Production build (`.output/`) |
| `npm run preview` | Serve the production build |
| `npm run typecheck` | `vue-tsc` type check |
| `npm run lint` | ESLint |

## e2e coverage

The Playwright suite lives at the repo root (`../e2e`) and is shared with the
Inertia app. Run the Nuxt-covered specs against this app with
`bash ../e2e/scripts/nuxt-smoke.sh` (see `../e2e/README.md`).

## Environment

`runtimeConfig` (in `nuxt.config.ts`) is driven by these env vars — see
`.env.example`:

| Var | Purpose | Local default |
|-----|---------|---------------|
| `NUXT_PUBLIC_API_BASE` | Browser-facing API base (incl. `/api`) | `http://localhost/api` |
| `NUXT_PUBLIC_BACKEND_ORIGIN` | Origin for the Sanctum CSRF-cookie + Fortify auth routes | `http://localhost` |
| `NUXT_API_BASE_SERVER` | Optional internal API base for SSR calls | falls back to `NUXT_PUBLIC_API_BASE` |
