#!/usr/bin/env bash
set -euo pipefail

# Nuxt pipeline smoke (issue #19, Phase 2/3). Boots the seeded Laravel JSON API
# (:8000) plus the built Nuxt frontend (:3000) and runs the Playwright specs
# that already have Nuxt coverage against the Nuxt origin.
#
# This is a NON-GATING complement to the Inertia e2e gate (e2e/scripts/start-app.sh),
# which is untouched and still points Playwright at the live Inertia app. As
# pages are migrated in later phases, widen GREP / SPEC_FILTER to match.
#
# Local usage (from repo root):
#   bash e2e/scripts/nuxt-smoke.sh
# Env overrides: NUXT_PORT, BACKEND_PORT, SPEC_FILTER, GREP

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

NUXT_PORT="${NUXT_PORT:-3000}"
BACKEND_PORT="${BACKEND_PORT:-8000}"
# Home (Phase 2) + People/Person/Pictures/Stories/404/SEO (Phase 3) are migrated
# to Nuxt, i.e. the entire public.spec.ts file — no --grep filter needed.
SPEC_FILTER="${SPEC_FILTER:-public.spec.ts}"
GREP="${GREP:-}"

pids=()
cleanup() {
  for pid in "${pids[@]:-}"; do
    kill "$pid" >/dev/null 2>&1 || true
  done
}
trap cleanup EXIT

# --- Backend: seeded Laravel API on :8000 -----------------------------------
if [[ ! -f .env.e2e ]]; then
  cp .env.e2e.example .env.e2e
fi
if ! grep -q '^APP_KEY=base64:' .env.e2e; then
  KEY="$(php -r "echo 'base64:'.base64_encode(random_bytes(32));")"
  if grep -q '^APP_KEY=' .env.e2e; then
    sed -i.bak "s|^APP_KEY=.*|APP_KEY=${KEY}|" .env.e2e && rm -f .env.e2e.bak
  else
    echo "APP_KEY=${KEY}" >> .env.e2e
  fi
fi

# Export .env.e2e into this process only (never touches the developer .env).
while IFS= read -r line || [[ -n "$line" ]]; do
  [[ -z "$line" || "$line" =~ ^# ]] && continue
  key="${line%%=*}"
  value="${line#*=}"
  value="${value%\"}"
  value="${value#\"}"
  export "${key}=${value}"
done < .env.e2e

# Trust the Nuxt origin as a stateful SPA (overrides the blank .env.e2e
# defaults). Harmless for the public Home smoke; required once authed pages
# arrive in later phases.
export E2E_HELPERS=true
export FRONTEND_URLS="http://localhost:${NUXT_PORT}"
export SANCTUM_STATEFUL_DOMAINS="localhost:${NUXT_PORT}"

touch database/e2e.sqlite
mkdir -p storage/app/public storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
php artisan config:clear >/dev/null
php artisan storage:link >/dev/null 2>&1 || true
php artisan migrate:fresh --seed --force
php artisan serve --host=127.0.0.1 --port="${BACKEND_PORT}" --no-reload &
pids+=($!)

# --- Frontend: Nuxt build + preview on :3000 --------------------------------
export NUXT_PUBLIC_API_BASE="http://localhost:${BACKEND_PORT}/api"
export NUXT_PUBLIC_BACKEND_ORIGIN="http://localhost:${BACKEND_PORT}"

pushd frontend >/dev/null
[[ -d node_modules ]] || npm ci
npm run build
PORT="${NUXT_PORT}" node .output/server/index.mjs &
pids+=($!)
popd >/dev/null

# --- Wait for both, then run the Nuxt-covered specs -------------------------
echo "Waiting for Laravel API on :${BACKEND_PORT}..."
until curl -sf "http://127.0.0.1:${BACKEND_PORT}/api/home" >/dev/null 2>&1; do sleep 1; done
echo "Waiting for Nuxt on :${NUXT_PORT}..."
until curl -sf -o /dev/null "http://127.0.0.1:${NUXT_PORT}/" >/dev/null 2>&1; do sleep 1; done

echo "Running Nuxt-covered specs (${SPEC_FILTER}${GREP:+ --grep $GREP}) against Nuxt..."
if [[ -n "${GREP}" ]]; then
  E2E_BASE_URL="http://localhost:${NUXT_PORT}" npx playwright test "${SPEC_FILTER}" --grep "${GREP}"
else
  E2E_BASE_URL="http://localhost:${NUXT_PORT}" npx playwright test "${SPEC_FILTER}"
fi
