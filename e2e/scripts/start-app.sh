#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"
cd "$ROOT"

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

if [[ ! -f public/build/manifest.json ]]; then
  npm run build
fi

# Export .env.e2e into this process only (never copies over the developer's .env).
# Laravel will not override existing process env with .env file values.
while IFS= read -r line || [[ -n "$line" ]]; do
  [[ -z "$line" || "$line" =~ ^# ]] && continue
  key="${line%%=*}"
  value="${line#*=}"
  value="${value%\"}"
  value="${value#\"}"
  export "${key}=${value}"
done < .env.e2e

export E2E_HELPERS=true

touch database/e2e.sqlite
mkdir -p storage/app/public storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
# Avoid leaving a cached config that would poison later PHPUnit runs if someone
# accidentally runs artisan against the developer .env after this script.
php artisan config:clear >/dev/null
php artisan storage:link >/dev/null 2>&1 || true
php artisan migrate:fresh --seed --force
exec php artisan serve --host=127.0.0.1 --port=8000 --no-reload
