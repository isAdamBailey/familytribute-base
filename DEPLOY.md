# Deploying to Forge (single-origin topology)

This app has multiple Forge sites (one per family), each running this same repo/branch with Forge's auto-deploy-on-push already configured — nothing about *triggering* deploys changes. What changes is what each site's Deploy Script, Daemons, Nginx config, and Environment need to look like, because Laravel is now a JSON API only (no pages of its own — issue #19 Phase 6) with Nuxt as the frontend.

**This is Forge's standard, officially-documented pattern for Node.js apps** — a Daemon running the Node process, with Nginx `proxy_pass`ing to it — not a workaround. Laravel's own guide for deploying Next.js to Forge uses the identical shape (see [Deploying your Next.js App to Forge](https://laravel.com/blog/deploying-your-nextjs-app-to-forge)). Forge has no concept of "a Laravel API + a Node frontend sharing one site," so nothing more automatic exists for this specific split; the `/api` + `/sanctum` carve-out in step 4 is what's specific to this app.

That official guide applies its Nginx changes via a reusable [Nginx Template](https://forge.laravel.com/docs/servers/nginx-templates) (Server → Nginx Templates) rather than editing a site's generated config directly, and selects that template when *creating* the site. Since your two sites already exist, it's unclear from Forge's docs whether an existing site can be switched onto a template after the fact — worth checking Forge's UI for that option (a site's Nginx settings may offer a "template" source you can swap to), since it would survive SSL/domain changes better than a direct edit. Step 4 below gives the direct-edit version as the version guaranteed to work regardless.

**Apply every step below to each existing Forge site**, substituting that site's own domain wherever `your-domain.com` appears. The steps are identical across sites; only the domain differs.

```
Browser
  │
  ▼
nginx (your-domain.com — existing Forge site, domain/SSL unchanged)
  ├─ /api/*, /sanctum/csrf-cookie  →  PHP-FPM  →  Laravel (public/index.php)
  └─ everything else               →  proxy_pass 127.0.0.1:3000  →  Node (Nuxt .output/server/index.mjs)
```

## 1. Site settings (Forge → site → **General**)

| Setting | Value |
|---|---|
| Web Directory | `/public` — **unchanged**. Laravel's own public dir; still the nginx `root` for the PHP-FPM branch below. |
| PHP Version | 8.3 (unchanged) |

Nothing here needs to change — Laravel still lives at the same path, still boots from `public/index.php`. What changes is which requests nginx sends there.

## 2. Add a Daemon for Nuxt (Forge → site → **Daemons**, or Server → Daemons)

Create a new daemon:

| Field | Value |
|---|---|
| Command | `NUXT_PUBLIC_API_BASE=https://your-domain.com/api NUXT_PUBLIC_BACKEND_ORIGIN=https://your-domain.com bash deploy/start-nuxt.sh` |
| Directory | `/home/forge/your-domain.com/frontend` |
| User | `forge` |

Forge's Daemon feature runs a raw command under Supervisor — it does **not** reliably load the site's `.env` or offer per-daemon env vars in its UI, so rather than depend on that, this site's own domain is set directly on the Command line (each of the two sites needs its own value here). `frontend/deploy/start-nuxt.sh` (already in the repo) fails loudly if these aren't set, rather than silently booting against the wrong domain.

Supervisor restarts the daemon automatically if it crashes; after the *first* deploy on a given site, restart it manually once from the Daemons tab so it picks up the freshly built `.output/`.

## 3. Deploy Script (Forge → site → **App / Deploy Script**)

Replace the existing script (it still has Vite/npm build steps for the removed Inertia frontend) with:

```bash
cd /home/forge/your-domain.com

git pull origin $FORGE_SITE_BRANCH

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

if [ -f artisan ]; then
    $FORGE_PHP artisan migrate --force
    $FORGE_PHP artisan config:cache
    $FORGE_PHP artisan route:cache
    $FORGE_PHP artisan view:cache
fi

cd frontend
npm ci
npm run build

sudo supervisorctl restart your-domain.com-nuxt:*
```

The `supervisorctl restart` name is whatever Forge names the daemon you created in step 2 for *that* site — check the exact program name on its Daemons tab (Forge shows it once created) and match it here, or the deploy will build a new `.output/` that the running Node process never picks up.

`route:cache` is safe — verified during Phase 1 (issue #19) that `/api/*` and Fortify's `/api`-prefixed routes cache and resolve correctly.

## 4. Nginx configuration (Forge → site → **Files → Edit Nginx Configuration**)

Forge's default site template puts a catch-all `location / { try_files $uri $uri/ /index.php?$query_string; }` block that sends every request into Laravel. Replace that one block with three: two specific ones for the paths Laravel still answers, and a proxy catch-all for Nuxt. Leave everything else in the file (the `location ~ \.php$` block, SSL directives, `server_name`, etc.) untouched — the `.php$` block is still needed, since `/api`'s `try_files` rewrite still lands on `index.php`.

Before (Forge default):
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

After:
```nginx
location /api {
    try_files $uri $uri/ /index.php?$query_string;
}

location /sanctum/csrf-cookie {
    try_files $uri $uri/ /index.php?$query_string;
}

location / {
    proxy_pass http://127.0.0.1:3000;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection 'upgrade';
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $scheme;
    proxy_cache_bypass $http_upgrade;
}
```

That's the entire backend surface — Fortify is registered under `/api` (`config/fortify.php`'s `prefix`), so login/register/logout/password-reset/2FA/email-verification all fall under the first `location /api` block already; nothing else needs its own block.

**Caveat:** Forge regenerates parts of this file when you change the domain, reissue an SSL certificate, or recreate the site — those actions can silently revert this edit back to the default catch-all. After any such action on a site, re-check that site's Nginx config and reapply the three blocks above if needed.

## 5. Environment variables (Forge → site → **Environment**)

Same-origin production means the Nuxt-facing config is just that site's own URL — no separate API host, no cross-origin CORS/cookie wiring:

```dotenv
APP_URL=https://your-domain.com

# Same-origin in production: FRONTEND_URLS can stay blank (it falls back to
# APP_URL — see App\Support\FrontendUrls) or be set explicitly to the same
# value. SANCTUM_STATEFUL_DOMAINS / SESSION_DOMAIN can stay blank too — same-
# origin requests don't need cross-domain cookie/CORS config; only fill these
# in if Nuxt is ever moved to a different host than the API.
FRONTEND_URLS=https://your-domain.com
SANCTUM_STATEFUL_DOMAINS=your-domain.com
SESSION_DOMAIN=
```

(`NUXT_PUBLIC_API_BASE`/`NUXT_PUBLIC_BACKEND_ORIGIN` are set on the Daemon's Command line — step 2 — not here, since Forge's Environment tab isn't confirmed to reach Daemon processes.)

No other Laravel env vars change — DB, S3, mail config are all untouched by this migration.

## 6. Checklist — repeat for each site

- [ ] Daemon created (step 2), with `NUXT_PUBLIC_API_BASE`/`NUXT_PUBLIC_BACKEND_ORIGIN` set to *this site's* domain on the Command line.
- [ ] Deploy Script updated (step 3) with the correct `supervisorctl restart` program name for *this site's* daemon.
- [ ] Nginx edited (step 4) — `/api` and `/sanctum/csrf-cookie` to PHP-FPM, `/` proxied to `127.0.0.1:3000`.
- [ ] Remaining env vars set (step 5).
- [ ] Deploy (or trigger one), then smoke-test:
  - `curl -s -o /dev/null -w '%{http_code}\n' https://your-domain.com/api/home` → `200`
  - `curl -s -o /dev/null -w '%{http_code}\n' https://your-domain.com/` → `200`, served by Nuxt (view source: `<div id="__nuxt">`)
  - Log in through the real UI once — this exercises the `/api/login` → Sanctum session cookie → same-origin round trip end to end.
- [ ] Confirm the Nuxt daemon survives a server reboot (Supervisor should restart it automatically; verify via Forge's Daemons tab after any reboot).
