# Deploying to Forge (single-origin topology)

This app has two Forge sites — **bailey.familytribute.org** and **hansen.familytribute.org** — both running this same repo/branch with Forge's auto-deploy-on-push already configured. Nothing about *triggering* deploys changes. What changes is what each site's Deploy Script, Daemons, Nginx config, and Environment need to look like, because Laravel is now a JSON API only (no pages of its own — issue #19 Phase 6) with Nuxt as the frontend.

**This is Forge's standard, officially-documented pattern for Node.js apps** — a Daemon running the Node process, with Nginx `proxy_pass`ing to it — not a workaround. Laravel's own guide for deploying Next.js to Forge uses the identical shape (see [Deploying your Next.js App to Forge](https://laravel.com/blog/deploying-your-nextjs-app-to-forge)). Forge has no concept of "a Laravel API + a Node frontend sharing one site," so nothing more automatic exists for this specific split; the `/api` + `/sanctum` carve-out in step 4 is what's specific to this app.

That official guide applies its Nginx changes via a reusable [Nginx Template](https://forge.laravel.com/docs/servers/nginx-templates) (Server → Nginx Templates) rather than editing a site's generated config directly, and selects that template when *creating* the site. Since both sites already exist, it's unclear from Forge's docs whether an existing site can be switched onto a template after the fact — worth checking Forge's UI for that option (a site's Nginx settings may offer a "template" source you can swap to), since it would survive SSL/domain changes better than a direct edit. Step 4 below gives the direct-edit version as the version guaranteed to work regardless.

**Apply every step below to both sites** — the steps are identical, only the domain (and, importantly, the port — see below) differ. Each `<domain>` placeholder below is either `bailey.familytribute.org` or `hansen.familytribute.org`, matching whichever site you're on.

```
Browser
  │
  ▼
nginx (<domain> — existing Forge site, domain/SSL unchanged)
  ├─ /api/*, /sanctum/csrf-cookie  →  PHP-FPM  →  Laravel (public/index.php)
  └─ everything else               →  proxy_pass 127.0.0.1:<port>  →  Node (Nuxt .output/server/index.mjs)
```

**Both sites live on the same physical Forge server**, confirmed while setting this up — meaning both Nuxt processes run on that one box and **cannot both use the same port**. That server already had several unrelated things bound in the "common default" 3000s range before this migration touched anything: a PM2 process on `*:3000`, and another pre-existing Forge-managed Node app (a Supervisor daemon, unrelated to this app) on `127.0.0.1:3001`. `sudo ss -tlnp | grep -E ':(300[0-9]|301[0-9]|400[0-9])\b'` confirmed those are the *only* two ports taken in that whole range — everything else scanned (`3002`–`3019`, `4000`–`4009`) was free at the time. Assigned ports, avoiding both:

| Site | Port |
|---|---|
| bailey.familytribute.org | `3002` |
| hansen.familytribute.org | `3003` |

If you ever add a third Node-based site to this same server, give it `3004` — but **verify it's actually free first** with `sudo lsof -i :3004` (or `sudo ss -tlnp | grep :3004`) rather than assuming; this server has repeatedly turned out to have more pre-existing occupants in this range than expected, and Forge/Supervisor won't warn you about a collision with something outside its own management.

## 0. Confirm auto-deploy is on (per site)

Forge calls this **"Push to deploy"** — a toggle on each site's **Deployments** tab. It's on by default for sites created from GitHub/GitLab/Bitbucket, and each Forge site registers its own webhook against the repo, so pushing once to the branch triggers *both* sites' deploys independently (each runs its own copy of that site's Deploy Script, on its own server/container) — you don't need to do anything extra to make them "trigger individually," that's already how one push to two sites works. Nothing below changes this; it only changes what each site's Deploy Script actually *does*.

To verify: on each site's **Deployments** tab, confirm "Push to deploy" is enabled and the branch shown matches what you push to (also set on that site's **General** tab). If you want to double check the webhook itself, your GitHub repo's **Settings → Webhooks** should list one entry per Forge site.

## 1. Site settings (Forge → site → **General**)

| Setting | Value |
|---|---|
| Web Directory | `/public` — **unchanged**. Laravel's own public dir; still the nginx `root` for the PHP-FPM branch below. |
| PHP Version | 8.3 (unchanged) |

Nothing here needs to change — Laravel still lives at the same path, still boots from `public/index.php`. What changes is which requests nginx sends there.

## 2. Add a Daemon for Nuxt (Forge → site → **Daemons**, or Server → Daemons)

Create a new daemon on each site. Copy the **Command** value from the fenced code block below (not a table — long lines in Markdown tables can get a stray space/line-break inserted when copied from a rendered wide table cell, which breaks the command silently), using **Directory** = that site's `frontend` folder and **User** = `forge`.

**bailey.familytribute.org** (port `3002`)
- Directory: `/home/forge/bailey.familytribute.org/frontend`
- Command:
  ```
  env PORT=3002 NUXT_PUBLIC_API_BASE=https://bailey.familytribute.org/api NUXT_PUBLIC_BACKEND_ORIGIN=https://bailey.familytribute.org bash deploy/start-nuxt.sh
  ```

**hansen.familytribute.org** (port `3003`)
- Directory: `/home/forge/hansen.familytribute.org/frontend`
- Command:
  ```
  env PORT=3003 NUXT_PUBLIC_API_BASE=https://hansen.familytribute.org/api NUXT_PUBLIC_BACKEND_ORIGIN=https://hansen.familytribute.org bash deploy/start-nuxt.sh
  ```

Before saving, check whether a daemon already exists for this site from earlier troubleshooting (STOPPED, FATAL, or otherwise) — **delete it** rather than leaving a duplicate; only one Supervisor program should exist per site's Nuxt process, or you'll get spurious port/spawn errors from the orphaned one.

**Directory must be the `frontend` subfolder, not the site root** — `/home/forge/<domain>/frontend`, never `/home/forge/<domain>/`. The Command is a *relative* path (`bash deploy/start-nuxt.sh`), resolved against whatever Directory sets as the working directory; get this wrong and Supervisor fails with `bash: deploy/start-nuxt.sh: No such file or directory` even though the file exists (it's just looking in the wrong place — confirm with `ls /home/forge/<domain>/frontend/deploy/` if this happens).

**Editing a daemon's fields in Forge's UI appears to create a *new* daemon entry (new `daemon-<id>`) rather than updating the existing one in place** — this is why the id can change every time you edit Directory/Command/Port for the same site (we saw `974115` → `974119` → `974168` for the same Bailey daemon across one troubleshooting session). After *any* edit to a daemon in Forge's UI: (1) re-check its id on the Daemons tab, (2) update that site's Deploy Script (step 3) if the `supervisorctl restart daemon-<id>` line references the old id, (3) delete the now-orphaned old daemon entry so it doesn't linger as a stopped/failed process.

After pasting, double-check the Command field in Forge shows the full, unbroken domain (`familytribute.org`, not split mid-word) before saving — a stray space or line break anywhere in it will make `env` misparse it (e.g. `env: 'ute.org': No such file or directory` if it splits mid-domain).

Forge's Daemon feature runs a raw command under Supervisor — it does **not** reliably load the site's `.env` or offer per-daemon env vars in its UI, so rather than depend on that, each site's own domain is set directly on its Command line above. The leading `env` matters: Supervisor execs the command directly with no shell, so bare `VAR=value another=value cmd` (which relies on shell parsing) fails with `can't find command 'VAR=value...'`; `env` is a real binary that sets the vars and execs the rest of the line itself, no shell needed. `frontend/deploy/start-nuxt.sh` (already in the repo) fails loudly if these aren't set, rather than silently booting against the wrong domain.

Supervisor restarts the daemon automatically if it crashes; after the *first* deploy on a given site, restart it manually once from the Daemons tab so it picks up the freshly built `.output/`.

## 3. Deploy Script (Forge → site → **App / Deploy Script**)

Replace the existing script (it still has Vite/npm build steps for the removed Inertia frontend) with — substitute `<domain>` for the site you're editing. This keeps the `git reset --hard && git clean -df` and Composer/FPM-reload steps from each site's existing script (both sites already had this pattern working before the Nuxt migration):

```bash
cd /home/forge/<domain>

git reset --hard && git clean -df

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
# Serialize Nuxt install/build across both sites on this shared Forge box.
# Without the flock, a single push triggers bailey + hansen npm ci at once and
# the kernel OOMs (`Killed`) mid-install. maxsockets=1 is also in frontend/.npmrc.
(
  flock -w 900 9 || exit 1
  npm ci
  NODE_OPTIONS=--max-old-space-size=2048 npm run build
) 9>/tmp/familytribute-frontend-build.lock

sudo supervisorctl restart daemon-<id>:*
```

Forge names each daemon `daemon-<id>` (a numeric id it assigns, e.g. `daemon-974115` — confirmed from a real BACKOFF log on this app, not `<domain>-nuxt` as might be guessed). Get the exact id from that site's Daemons tab once the daemon exists, and use it here — the deploy script needs the *exact* match, or it'll build a fresh `.output/` that the still-running old Node process never picks up.

`route:cache` is safe — verified during Phase 1 (issue #19) that `/api/*` and Fortify's `/api`-prefixed routes cache and resolve correctly.

## 4. Nginx configuration (Forge → site → **Files → Edit Nginx Configuration**)

Forge's default site template puts a catch-all `location / { try_files $uri $uri/ /index.php?$query_string; }` block that sends every request into Laravel. Replace that one block with the ones below. Leave everything else in the file (the `location ~ \.php$` block, SSL directives, `server_name`, etc.) untouched — the `.php$` block is still needed, since `/api`'s `try_files` rewrite still lands on `index.php`.

**Use the port from the table in the intro for whichever site you're editing** (bailey → `3002`, hansen → `3003`) — this is the one piece that differs between the two sites' nginx configs, since they share a server and can't both proxy to `3000`.

Before (Forge default):
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

After (shown for bailey.familytribute.org — swap `3002` for `3003` on hansen's):
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

That's the entire backend surface — Fortify is registered under `/api` (`config/fortify.php`'s `prefix`), so login/register/logout/password-reset/2FA/email-verification all fall under the first `location /api` block already; nothing else needs its own block. `location /_nuxt/` is optional but recommended — Nuxt's own static assets there are content-hashed and immutable, safe to cache aggressively.

**Caveat:** Forge regenerates parts of this file when you change the domain, reissue an SSL certificate, or recreate the site — those actions can silently revert this edit back to the default catch-all. After any such action on a site, re-check that site's Nginx config and reapply the three blocks above if needed.

## 5. Environment variables (Forge → site → **Environment**)

Same-origin production means the Nuxt-facing config is just that site's own URL — no separate API host, no cross-origin CORS/cookie wiring. Since `bailey.` and `hansen.` are different subdomains, each site's session/CORS config is independent of the other's:

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

`FRONTEND_URLS`/`SANCTUM_STATEFUL_DOMAINS`/`SESSION_DOMAIN` can technically stay blank (same-origin requests don't need cross-domain cookie/CORS config, and `FRONTEND_URLS` falls back to `APP_URL` — see `App\Support\FrontendUrls`), but setting them explicitly here removes any ambiguity.

(`NUXT_PUBLIC_API_BASE`/`NUXT_PUBLIC_BACKEND_ORIGIN` are set on each Daemon's Command line — step 2 — not here, since Forge's Environment tab isn't confirmed to reach Daemon processes.)

No other Laravel env vars change — DB, S3, mail config are all untouched by this migration.

**`GOOGLE_SITE_TAG` (each site's GA4 measurement ID) stays where it is, in this Environment tab.** The cutover deleted the blade template that rendered the analytics tag *and* the `services.google` config entry that fed it, leaving the var orphaned in each site's environment; it's wired up again now, so confirm it's still set to that site's own property (Bailey and Hansen report separately). Laravel serves it to Nuxt on `GET /api/site-settings` — deliberately *not* a `NUXT_PUBLIC_*` var on the Daemon command line, so the ID lives in one place per site and analytics needs no daemon reconfiguration. It only ships when `APP_ENV=production`; blank means the site runs without analytics.

## 6. Checklist — repeat for both sites

- [ ] No leftover/duplicate daemon from earlier troubleshooting on either site (`sudo supervisorctl status` on the server — one Nuxt daemon per site, not two).
- [ ] Daemon created (step 2) on bailey.familytribute.org — `PORT=3002` on the Command line, Directory = its `frontend` folder.
- [ ] Daemon created (step 2) on hansen.familytribute.org — `PORT=3003` on the Command line, Directory = its `frontend` folder.
- [ ] Both daemons manually started at least once from the Daemons tab and confirmed `RUNNING` (not just created — Supervisor won't auto-start a brand new daemon).
- [ ] Deploy Script updated (step 3) on both sites, each with the correct `supervisorctl restart daemon-<id>` program name for that site's daemon (get the id from its Daemons tab after creating it).
- [ ] Nginx edited (step 4) on both sites — `/api` and `/sanctum/csrf-cookie` to PHP-FPM, `/` (and `/_nuxt/`) proxied to that site's own port (bailey → `127.0.0.1:3002`, hansen → `127.0.0.1:3003` — **not** the same port on both).
- [ ] Env vars set (step 5) on both sites.
- [ ] Deploy both (or trigger a deploy), then smoke-test each:
  - `curl -s -o /dev/null -w '%{http_code}\n' https://bailey.familytribute.org/api/home` → `200`
  - `curl -s -o /dev/null -w '%{http_code}\n' https://bailey.familytribute.org/` → `200`, served by Nuxt (view source: `<div id="__nuxt">`) — **and check it's actually Bailey's content, not another site's** (this bit us once already from the port collision).
  - Repeat both for `hansen.familytribute.org`.
  - Log in through the real UI once per site — this exercises the `/api/login` → Sanctum session cookie → same-origin round trip end to end.
- [ ] Confirm both Nuxt daemons survive a server reboot (Supervisor should restart them automatically; verify via Forge's Daemons tab after any reboot).

## 7. Troubleshooting reference

Every error actually hit setting up Bailey, in the order you're likely to see them, with the exact fix:

| Symptom | Cause | Fix |
|---|---|---|
| `FATAL can't find command 'NUXT_PUBLIC_API_BASE=...'` | Supervisor execs the command directly, no shell — `VAR=value cmd` needs a shell to parse | Prefix the whole command with `env` (step 2) |
| `env: 'ute.org': No such file or directory` (domain split mid-word) | Long command copied from a rendered Markdown **table** cell picked up a stray line-break at the visual wrap point | Copy from a fenced code block instead (this doc uses those, not tables, for exactly this reason); after pasting, verify the domain isn't split before saving |
| `BACKOFF: Exited too quickly` | `.output/` doesn't exist yet — `node .output/server/index.mjs` fails instantly | Run a full deploy first (step 3 builds it), *then* start the daemon |
| `ERROR (spawn error)` | The daemon was never successfully started even once, or is in a broken state from an earlier attempt | Manually click **Start** on the daemon in Forge's UI and confirm `RUNNING` before relying on `supervisorctl restart` in the deploy script |
| `bash: deploy/start-nuxt.sh: No such file or directory` | Daemon's **Directory** is the site root, not `.../frontend` — the command's relative path resolves against the wrong cwd | Fix Directory to `/home/forge/<domain>/frontend`; confirm with `ls /home/forge/<domain>/frontend/deploy/` that the file is actually there first |
| `npm ci` / deploy ends with `Killed` (or Forge “timed out”) | OOM — Nuxt `npm ci` is heavy, and **both sites share one server** so a single push often runs two installs at once. The `glob@10` deprecation warning is unrelated noise. | Use the memory-safe deploy snippet in step 3 (`maxsockets=1` via `frontend/.npmrc`, `NODE_OPTIONS=--max-old-space-size=2048`, optional shared flock). Add swap on the Forge server if it still OOMs. Do **not** run root `npm ci` on deploy — only `cd frontend && npm ci`. |
| `nginx 502 Bad Gateway` | Nothing is listening on the port nginx is proxying to (daemon not running, or nginx wasn't reloaded after a config edit) | `sudo supervisorctl status` to check the daemon's actually `RUNNING`; `sudo nginx -t && sudo service nginx reload` if you edited nginx config outside Forge's UI |
| Site loads but shows **a different site's content** | Port collision — nginx reached *something* on that port, just not this app (another site, or an unrelated process like PM2) | `sudo ss -tlnp \| grep :<port>` to see what's actually there; assign this site an unused port instead (see the port table near the top) |
| `EADDRINUSE: address already in use 127.0.0.1:<port>` | Two processes trying to bind the same port — either a duplicate daemon for this site, or another site/process already using it | `sudo supervisorctl status` for duplicates of this site's daemon (delete the extra); `sudo ss -tlnp \| grep :<port>` to check for unrelated occupants before assuming a port is free |

**General lesson from all of the above**: this server had far more pre-existing occupants of the "obvious" ports (`3000`, `3001`) than expected, and Forge's daemon UI seems to spawn a new `daemon-<id>` on edits rather than updating in place. Verify — don't assume — every port and every daemon id before wiring it into a Deploy Script.
