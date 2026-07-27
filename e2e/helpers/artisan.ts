import { execSync } from 'node:child_process';
import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');

function loadE2eEnv(): NodeJS.ProcessEnv {
  const envPath = path.join(root, '.env.e2e');
  const env: NodeJS.ProcessEnv = { ...process.env, E2E_HELPERS: 'true' };

  if (!fs.existsSync(envPath)) {
    return env;
  }

  for (const line of fs.readFileSync(envPath, 'utf8').split('\n')) {
    const trimmed = line.trim();
    if (!trimmed || trimmed.startsWith('#')) {
      continue;
    }

    const eq = trimmed.indexOf('=');
    if (eq === -1) {
      continue;
    }

    const key = trimmed.slice(0, eq);
    let value = trimmed.slice(eq + 1);
    if (
      (value.startsWith('"') && value.endsWith('"')) ||
      (value.startsWith("'") && value.endsWith("'"))
    ) {
      value = value.slice(1, -1);
    }
    // .env.e2e provides defaults; a value already in this process's env takes
    // priority (this loader runs as its own process, spawned directly by the
    // Playwright test runner rather than as a child of start-api.sh, so it
    // only ever sees what's on disk in .env.e2e — see .env.e2e.example for
    // the fixed FRONTEND_URLS/SANCTUM_STATEFUL_DOMAINS/APP_URL values it
    // relies on).
    if (process.env[key] === undefined) {
      env[key] = value;
    }
  }

  return env;
}

function runArtisan(args: string[]): string {
  const artisan = process.env.E2E_ARTISAN ?? 'php artisan';
  const command = `${artisan} ${args.map((arg) => JSON.stringify(arg)).join(' ')}`;
  const output = execSync(command, {
    cwd: root,
    encoding: 'utf8',
    env: loadE2eEnv(),
  });

  return output.trim().split('\n').filter(Boolean).at(-1) ?? '';
}

export function verificationUrl(email: string): string {
  return runArtisan(['e2e:signed-url', 'verification', email]);
}

export function passwordResetUrl(email: string): string {
  return runArtisan(['e2e:signed-url', 'password-reset', email]);
}

/** Ensure at least `count` marked pagination fixtures exist. Returns the resulting marked total. */
export function ensurePeople(count: number): number {
  return Number(runArtisan(['e2e:ensure-people', String(count)]));
}

/** Remove people created by `ensurePeople` so other specs keep the seed contract. */
export function cleanupPeople(): void {
  runArtisan(['e2e:ensure-people', '--cleanup']);
}
