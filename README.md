[![Laravel Forge Site Deployment Status](https://img.shields.io/endpoint?url=https%3A%2F%2Fforge.laravel.com%2Fsite-badges%2Fcdf11bfd-28a4-4cd3-bef6-eddf5aef2722%3Fdate%3D1&style=plastic)](https://forge.laravel.com)

## About FamilyTribute

[FamilyTribute](https://familytribute.org/) is software that allows families to share their history together,
publicly, and / or privately.

If you are interested in inquiring about FamilyTribute for your family, please contact me, and we can set one up free of charge.

## Local Development

- `sail up`
- `sail artisan migrate:fresh --seed`
- `npm run dev`
- Navigate to http://localhost/

## Frontend migration (Nuxt 3/4 — in progress)

The frontend is being migrated from Vue 3 + Inertia to a standalone **Nuxt +
TypeScript** app that consumes the Laravel JSON API (`routes/api.php`). That
app lives in [`frontend/`](frontend/) and is a work in progress (issue #19); the
Inertia app in `resources/js` is still the live frontend until cutover. See
[`frontend/README.md`](frontend/README.md) to run it locally.


## License

The [Laravel framework](https://laravel.com) is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT),
But personally, I'd really rather you do not use this software for personal uses.
