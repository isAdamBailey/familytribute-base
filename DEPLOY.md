# Deploying to Forge (single-origin topology)

This documents the Forge settings for the post-cutover architecture (issue #19 Phase 6): **one Forge site**, at the same domain the old Inertia app used to serve from (`familytribute.org`). Nginx on that site routes `/api` and `/sanctum` to the existing Laravel app (PHP-FPM); everything else goes to a Node process running the Nuxt build. Laravel no longer renders any pages of its own, so nothing about the site's domain, DNS, or SSL cert needs to change — only the site's Nginx config, its Deploy Script, and a new Daemon for Nuxt.

```
Browser
  │
  ▼
nginx (familytribute.org, existing Forge site — unchanged domain/SSL)
  ├─ /api/*, /sanctum/csrf-cookie  →  PHP-FPM  →  Laravel (public/index.php)
  └─ everything else               →  proxy_pass 127.0.0.1:3000  →  Node (Nuxt .output/server/index.mjs)
```

## 1. Site settings (Forge → your site → **General**)

| Setting | Value |
|---|---|
| Web Directory | `/public` — **unchanged**. Laravel's own public dir; still the nginx `root` for the PHP-FPM branch below. |
| PHP Version | 8.3 (unchanged) |

Nothing here needs to change — the Laravel app still lives at the same path, still boots from `public/index.php`. What changes is which requests nginx sends there.

## 2. Add a Daemon for Nuxt (Forge → your site → **Daemons**, or Server → Daemons)

Create a new daemon:

| Field | Value |
|---|---|
| Command | `bash /home/forge/familytribute.org/frontend/deploy/start-nuxt.sh` |
| Directory | `/home/forge/familytribute.org/frontend` |
| User | `forge` |

Forge's Daemon feature runs a raw command under Supervisor — it does **not** load your site's `.env` or let you set per-daemon env vars in the UI, so the command needs to export what Nuxt needs itself. Add this script to the repo (it isn't there yet):

`frontend/deploy/start-nuxt.sh`:
```bash
#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."

export PORT=3000
export HOST=127.0.0.1
export NUXT_PUBLIC_API_BASE="https://familytribute.org/api"
export NUXT_PUBLIC_BACKEND_ORIGIN="https://familytribute.org"

exec node .output/server/index.mjs
```

`HOST=127.0.0.1` keeps Nuxt off the public interface — only nginx (same box) talks to it, on the loopback address, matching the `proxy_pass` below. Supervisor restarts the daemon automatically if it crashes; after the first deploy, restart it manually once from the Daemons tab so it picks up the freshly built `.output/`.

## 3. Deploy Script (Forge → your site → **App / Deploy Script**)

Replace the existing script (it still has Vite/npm build steps for the removed Inertia frontend — issue #19 Phase 2/6) with:

```bash
cd /home/forge/familytribute.org

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

sudo supervisorctl restart familytribute.org-nuxt:*
```

The `supervisorctl restart` name (`familytribute.org-nuxt`) is whatever Forge names the daemon you created in step 2 — check the exact program name on the Daemons tab (Forge shows it once created) and match it here, or the deploy will build a new `.output/` that the running Node process never picks up.

`route:cache` is safe — verified during Phase 1 (issue #19) that `/api/*` and Fortify's `/api`-prefixed routes cache and resolve correctly.

## 4. Nginx configuration (Forge → your site → **Files → Edit Nginx Configuration**)

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

**Caveat:** Forge regenerates parts of this file when you change the domain, reissue an SSL certificate, or recreate the site — those actions can silently revert this edit back to the default catch-all. After any such action, re-check this file and reapply the three blocks above if needed.

## 5. Environment variables (Forge → your site → **Environment**)

Same-origin production means the Nuxt-facing config is just the site's own URL — no separate API host, no cross-origin CORS/cookie wiring:

```dotenv
APP_URL=https://familytribute.org

# Same-origin in production: FRONTEND_URLS can stay blank (it falls back to
# APP_URL — see App\Support\FrontendUrls) or be set explicitly to the same
# value. SANCTUM_STATEFUL_DOMAINS / SESSION_DOMAIN can stay blank too — same-
# origin requests don't need cross-domain cookie/CORS config; only fill these
# in if Nuxt is ever moved to a different host than the API.
FRONTEND_URLS=https://familytribute.org
SANCTUM_STATEFUL_DOMAINS=familytribute.org
SESSION_DOMAIN=
```

No other Laravel env vars change — DB, S3, mail config are all untouched by this migration.

## 6. First deploy checklist

- [ ] `frontend/deploy/start-nuxt.sh` committed and executable (`chmod +x`).
- [ ] Daemon created (step 2), pointed at that script.
- [ ] Deploy Script updated (step 3) with the correct `supervisorctl restart` program name for the daemon.
- [ ] Nginx edited (step 4) — `/api` and `/sanctum/csrf-cookie` to PHP-FPM, `/` proxied to `127.0.0.1:3000`.
- [ ] Env vars set (step 5).
- [ ] Deploy, then smoke-test:
  - `curl -s -o /dev/null -w '%{http_code}\n' https://familytribute.org/api/home` → `200`
  - `curl -s -o /dev/null -w '%{http_code}\n' https://familytribute.org/` → `200`, served by Nuxt (view source: `<div id="__nuxt">`)
  - Log in through the real UI once — this exercises the `/api/login` → Sanctum session cookie → same-origin round trip end to end.
- [ ] Confirm the Nuxt daemon survives a server reboot (Supervisor should restart it automatically; verify via Forge's Daemons tab after any reboot).

## Why not two Forge sites (e.g. `api.familytribute.org` + `familytribute.org`)?

That's the more conventional Sanctum-SPA topology and avoids hand-edited nginx entirely, but it wasn't the direction chosen here — this app replaces the old Inertia frontend in the same place on the same domain it already lived, rather than introducing a new subdomain. The trade-off is the nginx file in step 4 is hand-maintained and can be clobbered by Forge's domain/SSL actions (see the caveat above); the two-site approach doesn't have that risk if it's ever worth revisiting.
