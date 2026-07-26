#!/usr/bin/env bash
set -euo pipefail

# Entry point for the Forge Daemon that keeps the Nuxt frontend running in
# production (see ../../DEPLOY.md). Forge's Daemon feature runs a raw command
# under Supervisor — it doesn't load the site's .env or offer per-daemon env
# vars in the UI, so NUXT_PUBLIC_API_BASE/NUXT_PUBLIC_BACKEND_ORIGIN must be
# set on the Daemon's Command line itself (each site has its own domain), not
# assumed here — see DEPLOY.md step 2.

cd "$(dirname "$0")/.."

: "${NUXT_PUBLIC_API_BASE:?Set NUXT_PUBLIC_API_BASE on the Forge Daemon's Command line (see DEPLOY.md)}"
: "${NUXT_PUBLIC_BACKEND_ORIGIN:?Set NUXT_PUBLIC_BACKEND_ORIGIN on the Forge Daemon's Command line (see DEPLOY.md)}"

export PORT="${PORT:-3000}"
export HOST="${HOST:-127.0.0.1}"

exec node .output/server/index.mjs
