#!/usr/bin/env bash
set -euo pipefail

# Entry point for the Forge Daemon that keeps the Nuxt frontend running in
# production (see ../../DEPLOY.md). Forge's Daemon feature runs a raw command
# under Supervisor — it doesn't load the site's .env or offer per-daemon env
# vars in the UI, so this script exports what Nuxt needs itself before
# execing the built server.

cd "$(dirname "$0")/.."

export PORT="${PORT:-3000}"
export HOST="${HOST:-127.0.0.1}"
export NUXT_PUBLIC_API_BASE="${NUXT_PUBLIC_API_BASE:-https://familytribute.org/api}"
export NUXT_PUBLIC_BACKEND_ORIGIN="${NUXT_PUBLIC_BACKEND_ORIGIN:-https://familytribute.org}"

exec node .output/server/index.mjs
