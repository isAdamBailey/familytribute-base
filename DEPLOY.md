# Deploying to Forge (single-origin topology)

This app has two Forge sites — **bailey.familytribute.org** and **hansen.familytribute.org** — both running this same repo/branch with Forge's auto-deploy-on-push already configured. Nothing about *triggering* deploys changes. What changes is what each site's Deploy Script, Daemons, Nginx config, and Environment need to look like, because Laravel is now a JSON API only (no pages of its own — issue #19 Phase 6) with Nuxt as the frontend.

**This is Forge's standard, officially-documented pattern for Node.js apps** — a Daemon running the Node process, with Nginx `proxy_pass`ing to it — not a workaround. Laravel's own guide for deploying Next.js to Forge uses the identical shape (see [Deploying your Next.js App to Forge](https://laravel.com/blog/deploying-your-nextjs-app-to-forge)). Forge has no concept of "a Laravel API + a Node frontend sharing one site," so nothing more automatic exists for this specific split; the `/api` + `/sanctum` carve-out in step 4 is what's specific to this app.

That official guide applies its Nginx changes via a reusable [Nginx Template](https://forge.laravel.com/docs/servers/nginx-templates) (Server → Nginx Templates) rather than editing a site's generated config directly, and selects that template when *creating* the site. Since both sites already exist, it's unclear from Forge's docs whether an existing site can be switched onto a template after the fact — worth checking Forge's UI for that option (a site's Nginx settings may offer a "template" source you can swap to), since it would survive SSL/domain changes better than a direct edit. Step 4 below gives the direct-edit version as the version guaranteed to work regardless.

**Apply every step below to both sites** — the steps are identical, only the domain differs. Each `<domain>` placeholder below is either `bailey.familytribute.org` or `hansen.familytribute.org`, matching whichever site you're on.

```
Browser
  │
  ▼
nginx (<domain> — existing Forge site, domain/SSL unchanged)
  ├─ /api/*, /sanctum/csrf-cookie  →  PHP-FPM  →  Laravel (public/index.php)
  └─ everything else               →  proxy_pass 127.0.0.1:3000  →  Node (Nuxt .output/server/index.mjs)
```

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

**bailey.familytribute.org**
- Directory: `/home/forge/bailey.familytribute.org/frontend`
- Command:
  ```
  env NUXT_PUBLIC_API_BASE=https://bailey.familytribute.org/api NUXT_PUBLIC_BACKEND_ORIGIN=https://bailey.familytribute.org bash deploy/start-nuxt.sh
  ```

**hansen.familytribute.org**
- Directory: `/home/forge/hansen.familytribute.org/frontend`
- Command:
  ```
  env NUXT_PUBLIC_API_BASE=https://hansen.familytribute.org/api NUXT_PUBLIC_BACKEND_ORIGIN=https://hansen.familytribute.org bash deploy/start-nuxt.sh
  ```

After pasting, double-check the Command field in Forge shows the full, unbroken domain (`familytribute.org`, not split mid-word) before saving — a stray space or line break anywhere in it will make `env` misparse it (e.g. `env: 'ute.org': No such file or directory` if it splits mid-domain).

Forge's Daemon feature runs a raw command under Supervisor — it does **not** reliably load the site's `.env` or offer per-daemon env vars in its UI, so rather than depend on that, each site's own domain is set directly on its Command line above. The leading `env` matters: Supervisor execs the command directly with no shell, so bare `VAR=value another=value cmd` (which relies on shell parsing) fails with `can't find command 'VAR=value...'`; `env` is a real binary that sets the vars and execs the rest of the line itself, no shell needed. `frontend/deploy/start-nuxt.sh` (already in the repo) fails loudly if these aren't set, rather than silently booting against the wrong domain.

Supervisor restarts the daemon automatically if it crashes; after the *first* deploy on a given site, restart it manually once from the Daemons tab so it picks up the freshly built `.output/`.

## 3. Deploy Script (Forge → site → **App / Deploy Script**)

Replace the existing script (it still has Vite/npm build steps for the removed Inertia frontend) with — substitute `<domain>` for the site you're editing:

```bash
cd /home/forge/<domain>

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

sudo supervisorctl restart <domain>-nuxt:*
```

The `supervisorctl restart` name is whatever Forge names the daemon you created in step 2 for *that* site — check the exact program name on its Daemons tab (Forge shows it once created) and match it here, or the deploy will build a new `.output/` that the running Node process never picks up.

`route:cache` is safe — verified during Phase 1 (issue #19) that `/api/*` and Fortify's `/api`-prefixed routes cache and resolve correctly.

## 4. Nginx configuration (Forge → site → **Files → Edit Nginx Configuration**)

Forge's default site template puts a catch-all `location / { try_files $uri $uri/ /index.php?$query_string; }` block that sends every request into Laravel. Replace that one block with three: two specific ones for the paths Laravel still answers, and a proxy catch-all for Nuxt. Leave everything else in the file (the `location ~ \.php$` block, SSL directives, `server_name`, etc.) untouched — the `.php$` block is still needed, since `/api`'s `try_files` rewrite still lands on `index.php`. This block is identical on both sites (no domain substitution needed here).

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

## 6. Checklist — repeat for both sites

- [ ] Daemon created (step 2) on bailey.familytribute.org, with its own domain on the Command line.
- [ ] Daemon created (step 2) on hansen.familytribute.org, with its own domain on the Command line.
- [ ] Deploy Script updated (step 3) on both sites, each with the correct `supervisorctl restart` program name for that site's daemon.
- [ ] Nginx edited (step 4) on both sites — `/api` and `/sanctum/csrf-cookie` to PHP-FPM, `/` proxied to `127.0.0.1:3000`.
- [ ] Env vars set (step 5) on both sites.
- [ ] Deploy both (or trigger a deploy), then smoke-test each:
  - `curl -s -o /dev/null -w '%{http_code}\n' https://bailey.familytribute.org/api/home` → `200`
  - `curl -s -o /dev/null -w '%{http_code}\n' https://bailey.familytribute.org/` → `200`, served by Nuxt (view source: `<div id="__nuxt">`)
  - Repeat both for `hansen.familytribute.org`.
  - Log in through the real UI once per site — this exercises the `/api/login` → Sanctum session cookie → same-origin round trip end to end.
- [ ] Confirm both Nuxt daemons survive a server reboot (Supervisor should restart them automatically; verify via Forge's Daemons tab after any reboot).
