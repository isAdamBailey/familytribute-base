[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2Fcdf11bfd-28a4-4cd3-bef6-eddf5aef2722%3Fdate%3D1&style=plastic)](https://forge.laravel.com)

## About FamilyTribute

[FamilyTribute](https://familytribute.org/) is software that allows families to share their history together,
publicly, and / or privately.

If you are interested in inquiring about FamilyTribute for your family, please contact me, and we can set one up free of charge.

## Architecture

This repo has two apps:

- The Laravel backend (repo root) — a JSON API only (`routes/api.php`), no server-rendered pages.
- [`frontend/`](frontend/) — the Nuxt 4 + TypeScript frontend, the only UI this app has.

See [`CLAUDE.md`](CLAUDE.md) for the full architecture rundown and [`DEPLOY.md`](DEPLOY.md) for the production (Forge) topology.

## Local Development

```bash
sail up
sail artisan migrate:fresh --seed
```

Then, in a separate terminal, run the frontend (see [`frontend/README.md`](frontend/README.md)):

```bash
cd frontend
npm install
npm run dev
```

Navigate to http://localhost:3000/.

## License

The [Laravel framework](https://laravel.com) is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT),
But personally, I'd really rather you do not use this software for personal uses.
