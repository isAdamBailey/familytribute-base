# Deploying to Forge (single-origin topology)

This app has two Forge sites — **bailey.familytribute.org** and **hansen.familytribute.org** — both running this same repo/branch, each with its own database, `.env`, Nuxt daemon and push-to-deploy webhook. One push to the branch deploys both.

> **Moving servers (issue #37).** This document describes the sites as they're set up on the **new** server (`134.199.176.96`, PHP 8.5, Node 24): Forge **zero-downtime** sites, where every deploy builds a fresh release and `current` is switched to it. Until each site's cutover in #37, the **old** server (`167.172.156.79`, PHP 8.3, Node 20) still runs it in the classic layout (code in the site root, deploy script that `git pull`s in place), described by [the previous version of this file](https://github.com/isAdamBailey/familytribute-base/blob/9e5e61c/DEPLOY.md). Follow #37 for the move itself; this file is the steady state afterwards.

Laravel is a JSON API only; Nuxt runs as a Forge daemon behind nginx, Forge's standard pattern for Node apps ([Deploying your Next.js App to Forge](https://laravel.com/blog/deploying-your-nextjs-app-to-forge)).

**Apply every step below to both sites** — only the domain and the port differ. Each `<domain>` placeholder is either `bailey.familytribute.org` or `hansen.familytribute.org`.

```
Browser
  │
  ▼
nginx (<domain>)
  ├─ /api/*, /sanctum/csrf-cookie  →  PHP-FPM  →  Laravel (current/public/index.php)
  └─ everything else               →  proxy_pass 127.0.0.1:<port>  →  Node (current/frontend/.output/server/index.mjs)
```

## Layout on the server

| Path | What |
|---|---|
| `/home/forge/<domain>/releases/<id>` | one directory per deploy |
| `/home/forge/<domain>/current` | symlink to the active release — **everything that runs outside the deploy script must go through it** |
| `/home/forge/<domain>/.env`, `/home/forge/<domain>/storage` | shared across releases (Forge links them into each one) |

Nothing persistent lives in a release: media is on S3, and sessions and cache are in the database or Redis.

## Ports

Both sites share a server with other apps, so each Nuxt process needs its own port. **Verify a port is free before using it** (`sudo ss -tlnp | grep :<port>`); Forge and Supervisor won't warn you about a collision with something outside their own management, and a collision shows up as one site serving another app's content.

| Port | Used by |
|---|---|
| `3002` | bailey.familytribute.org |
| `3003` | hansen.familytribute.org |
| `3004` | Black Circles (`records.adambailey.io`) — not ours, don't reuse |

## 0. Push to deploy (per site)

On each site's **Deployments** tab, "Push to deploy" is on and the branch matches the one you push to. Each Forge site registers its own webhook, so GitHub → Settings → Webhooks lists one per site.

## 1. Site settings

| Setting | Value |
|---|---|
| Web directory | `/public` |
| PHP version | 8.5 |
| Zero-downtime deployments | on (can only be chosen when the site is created) |

## 2. Nuxt daemon (Forge → site → **Background processes → Custom**)

One per site, **User** = `forge`, **Directory** = that site's `current/frontend`. Copy the **Command** from the code block, not from a table (a rendered table cell can pick up a stray line break that splits the domain).

**bailey.familytribute.org** (port `3002`)
- Directory: `/home/forge/bailey.familytribute.org/current/frontend`
- Command:
  ```
  env PORT=3002 NUXT_PUBLIC_API_BASE=https://bailey.familytribute.org/api NUXT_PUBLIC_BACKEND_ORIGIN=https://bailey.familytribute.org bash deploy/start-nuxt.sh
  ```

**hansen.familytribute.org** (port `3003`)
- Directory: `/home/forge/hansen.familytribute.org/current/frontend`
- Command:
  ```
  env PORT=3003 NUXT_PUBLIC_API_BASE=https://hansen.familytribute.org/api NUXT_PUBLIC_BACKEND_ORIGIN=https://hansen.familytribute.org bash deploy/start-nuxt.sh
  ```

- **Why `current`:** the command's relative `deploy/start-nuxt.sh` resolves against the Directory, and the site root has no code in a zero-downtime layout. Supervisor resolves `current` when the process starts, which is why the deploy script restarts the daemon after switching releases.
- **Why `env`:** Supervisor execs the command with no shell, so a bare `VAR=value cmd` doesn't parse.
- Forge daemons don't load the site's `.env`, so each site's domain is on its Command line. `frontend/deploy/start-nuxt.sh` fails loudly if either variable is missing.
- Server-side rendering can be pointed elsewhere with the server-only `runtimeConfig` keys in `frontend/nuxt.config.ts` (see `frontend/app/plugins/api.ts`). Not needed in steady state; #37 uses them to test a site before DNS points at it.
- A new process doesn't run until you click **Start**. Confirm with `sudo supervisorctl status`.
- **Editing a daemon in Forge can create a new `daemon-<id>`** (bailey once went `974115` → `974119` → `974168` in one session). After any edit: recheck the id, update the deploy script's restart line (step 3), and delete the orphaned entry.

## 3. Deploy script (Forge → site → **Deployments**)

Substitute that site's daemon id:

```bash
$CREATE_RELEASE()

cd "$FORGE_RELEASE_DIRECTORY"

$FORGE_COMPOSER install --no-dev --no-interaction --prefer-dist --optimize-autoloader

$FORGE_PHP artisan migrate --force
$FORGE_PHP artisan config:cache
$FORGE_PHP artisan route:cache

cd frontend
# One Node build at a time on this box: a push deploys both FamilyTribute
# sites at once, and two parallel `npm ci` runs get OOM-killed. maxsockets=1
# is also in frontend/.npmrc.
(
  flock -w 900 9 || exit 1
  npm ci
  NODE_OPTIONS=--max-old-space-size=2048 npm run build
) 9>/tmp/node-build.lock
cd ..

$ACTIVATE_RELEASE()

( flock -w 10 9 || exit 1
    echo 'Restarting FPM...'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

sudo supervisorctl restart daemon-<id>:*
```

- Keep the explicit `cd "$FORGE_RELEASE_DIRECTORY"`: Forge's docs say it's inserted automatically, but it wasn't on this account's servers.
- Everything before `$ACTIVATE_RELEASE()` runs in the new release while the old one keeps serving, so a failed build or migration leaves the live site untouched.
- No `artisan optimize` or `view:cache` (there are no views; see Troubleshooting).
- The FPM reload clears OPcache once `current` has moved; Forge has no macro for it.
- The daemon restart **must come after `$ACTIVATE_RELEASE()`**, or Node restarts on the previous release.
- No `$RESTART_QUEUES()`: no job implements `ShouldQueue`, so there are no workers.
- `route:cache` is safe — `/api/*` and Fortify's `/api`-prefixed routes cache and resolve correctly.
- Only `frontend/` gets `npm ci`. The root `package.json` is Playwright for e2e and isn't needed in production.

## 4. nginx (Forge → site → **nginx**)

Forge's default template has a catch-all `location / { try_files $uri $uri/ /index.php?$query_string; }` that sends every request into Laravel. Replace that one block with the ones below, using **this site's port** (bailey `3002`, hansen `3003`). Leave everything else — the `location ~ \.php$` block, SSL directives, `server_name` — untouched; `/api`'s `try_files` still lands on `index.php`.

Shown for bailey.familytribute.org:
```nginx
location /api {
    try_files $uri $uri/ /index.php?$query_string;
}

location /sanctum/csrf-cookie {
    try_files $uri $uri/ /index.php?$query_string;
}

location /_nuxt/ {
    proxy_pass http://127.0.0.1:3002;
    proxy_set_header Host $host;
    add_header Cache-Control "public, max-age=31536000, immutable";
}

location / {
    proxy_pass http://127.0.0.1:3002;
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

Fortify is registered under `/api` (`config/fortify.php`'s `prefix`), so login, register, password reset, 2FA and email verification all fall under the first block. `/_nuxt/` assets are content-hashed and safe to cache for a year.

**Caveat:** Forge regenerates parts of this file when you change a domain, issue a certificate or recreate the site, and can put the default catch-all back. Recheck after any of those.

## 5. Environment (Forge → site → **Environment**)

Each site's own URL, with no cross-origin wiring:

```dotenv
# bailey.familytribute.org:
APP_URL=https://bailey.familytribute.org
FRONTEND_URLS=https://bailey.familytribute.org
SANCTUM_STATEFUL_DOMAINS=bailey.familytribute.org
SESSION_DOMAIN=

# hansen.familytribute.org:
APP_URL=https://hansen.familytribute.org
FRONTEND_URLS=https://hansen.familytribute.org
SANCTUM_STATEFUL_DOMAINS=hansen.familytribute.org
SESSION_DOMAIN=
```

`FRONTEND_URLS` falls back to `APP_URL` (`App\Support\FrontendUrls`), but setting them explicitly removes any ambiguity. `DB_*` names each site's **own** database; a site pointed at the other's looks healthy and shows the other family.

`GOOGLE_SITE_TAG` is each site's own GA4 measurement ID (Bailey and Hansen report separately). Laravel serves it to Nuxt on `GET /api/site-settings`, so it lives here, not on the daemon's Command line. It only ships when `APP_ENV=production`; blank means no analytics.

## 6. Scheduler (Forge → server → **Scheduled jobs → Laravel Scheduler**)

One job per site, every minute, user `forge`:

```
php8.5 /home/forge/<domain>/current/artisan schedule:run
```

The `/current` matters: the site root has no `artisan`. Forge pre-fills an older PHP version; change it to `php8.5`. What runs, and when, is in `app/Console/Kernel.php`; it emails real family members, so each site gets exactly one job.

## 7. Checklist — both sites

- [ ] One Nuxt daemon per site (`sudo supervisorctl status`), each `RUNNING`, Directory `…/current/frontend`, its own port.
- [ ] Deploy script on both sites restarts **that site's** current `daemon-<id>`.
- [ ] nginx on both sites: `/api` and `/sanctum/csrf-cookie` to PHP-FPM, `/` and `/_nuxt/` proxied to that site's own port.
- [ ] Environment (step 5) and scheduler (step 6) on both sites.
- [ ] Smoke test each:
  - `curl -s -o /dev/null -w '%{http_code}\n' https://<domain>/api/home` → `200`
  - `curl -s -o /dev/null -w '%{http_code}\n' https://<domain>/` → `200`, served by Nuxt (view source: `<div id="__nuxt">`) — **and check it's that family's content, not the other site's**.
  - Log in through the real UI once per site — this exercises `/api/login` → Sanctum session cookie → same-origin round trip.
- [ ] Both daemons come back after a server reboot.

## 8. Troubleshooting

| Symptom | Cause | Fix |
|---|---|---|
| `FATAL can't find command 'NUXT_PUBLIC_API_BASE=...'` | Supervisor execs the command with no shell | Prefix the command with `env` (step 2) |
| `env: 'ute.org': No such file or directory` | The command was copied from a rendered table and split mid-domain | Copy from a code block; check the domain is whole before saving |
| `bash: deploy/start-nuxt.sh: No such file or directory` | Daemon Directory isn't `…/current/frontend` | Fix the Directory |
| `BACKOFF: Exited too quickly` | No `.output/` in the active release yet | Run a full deploy, then start the daemon |
| Status "unknown" / `ERROR (spawn error)` | The daemon was never started | Click **Start** in Forge and confirm `RUNNING` |
| Deploy fails at `supervisorctl restart daemon-…` | The id changed after a daemon edit | Use the id the Background processes tab shows now; delete the orphan |
| Deploy ends with `Killed` | OOM in `npm ci` / build (two sites deploy at once) | Check the build `flock` is in **both** sites' scripts; lower `--max-old-space-size`; add swap or memory |
| `artisan optimize` / `view:cache` fails on `resources/views` | API-only app | Use `config:cache` + `route:cache` (step 3) |
| nginx `502 Bad Gateway` | Nothing listening on the port, or nginx not reloaded | `sudo supervisorctl status`; `sudo nginx -t && sudo service nginx reload` |
| Site shows **a different site's content** | Port collision, or `DB_DATABASE` names the other site's database | `sudo ss -tlnp \| grep :<port>`; check the site's nginx port and `.env` |
| `EADDRINUSE: address already in use 127.0.0.1:<port>` | Duplicate daemon for this site, or another process on the port | Delete the duplicate; pick a free port if something else owns it |
