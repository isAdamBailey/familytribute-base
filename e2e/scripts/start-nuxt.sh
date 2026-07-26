#!/usr/bin/env bash
set -euo pipefail

# Boots the (already-built — see package.json's `build:frontend`) Nuxt
# frontend on :3000 for Playwright, paired with start-api.sh via
# playwright.config.ts's array `webServer` config. Building here too would
# serialize the Nuxt build behind start-api.sh's migrate/seed, since
# Playwright starts array `webServer` entries one at a time — building
# up front in `test:e2e` lets both servers come up quickly instead.
#
# Just sets the local URLs and delegates to the same entry point production
# uses (frontend/deploy/start-nuxt.sh) rather than duplicating it.

ROOT="$(cd "$(dirname "$0")/../.." && pwd)"

export NUXT_PUBLIC_API_BASE="http://localhost:8000/api"
export NUXT_PUBLIC_BACKEND_ORIGIN="http://localhost:8000"

exec bash "$ROOT/frontend/deploy/start-nuxt.sh"
