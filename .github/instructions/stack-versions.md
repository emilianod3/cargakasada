---
description: Describes the project stack, current versions in use, and best-practice notes for this environment.
globs:
  - "package.json"
  - "composer.json"
alwaysApply: false
---
# Project Stack and Versions

This project uses a Laravel + Inertia + Vue 3 + Vite stack with current support for PHP 8.2.

## Backend

- `php`: `^8.2`
- `laravel/framework`: `^12.0`
- `laravel/tinker`: `^2.10.1`
- `awobaz/compoships`: `^3.0`
- `inertiajs/inertia-laravel`: `^3.1`
- `owen-it/laravel-auditing`: `^14.0`
- `stevebauman/location`: `^7.6`
- `tightenco/ziggy`: `^2.6`

## Frontend

- `vue`: `^3.5.38`
- `@inertiajs/vue3`: `^3.4.0`
- `@vitejs/plugin-vue`: `^6.0.7`
- `vite`: `^8.0.16`
- `tailwindcss`: `^4.0.0`
- `@tailwindcss/vite`: `^4.0.0`
- `@tailwindcss/forms`: `^0.5.11`
- `@fortawesome/fontawesome-free`: `^7.2.0`
- `floating-vue`: `^5.2.2`
- `ziggy-js`: `^2.6.2`
- `moment`: `^2.30.1`

## Dev / Testing

- `phpunit/phpunit`: `^11.5.50`
- `barryvdh/laravel-ide-helper`: `^3.7`
- `fakerphp/faker`: `^1.23`
- `laravel/pint`: `^1.24`
- `laravel/sail`: `^1.41`
- `laravel/pail`: `^1.2.2`
- `mockery/mockery`: `^1.6`
- `nunomaduro/collision`: `^8.6`
- `concurrently`: `^9.0.1`
- `fs-extra`: `^11.3.5`

## Best-practice Notes

- Laravel 12 with PHP 8.2 is a modern, supported combination. Ensure `composer.lock` is version-controlled with the code to lock dependencies.
- `vite` 8 + `@vitejs/plugin-vue` 6 + `tailwindcss` 4 are compatible, but watch for configuration and syntax changes from Tailwind CSS v3.
- `axios` is listed under `devDependencies`; this is fine if it is only used in build scripts. If it is used at runtime in the frontend, move it to `dependencies`.
- `moment` is in maintenance mode and is not recommended for new features. Consider migrating to newer libraries such as `date-fns` or `luxon` when possible.
- `@tailwindcss/forms` version 0.5 should work with Tailwind CSS 4, but verify custom styles and import handling in the build pipeline.
- `phpunit` 11 requires PHP 8.1+; with PHP 8.2 this is already appropriate, but keep the test suite updated to avoid unsupported dependencies.
- `laravel/pint` 1.24 is suitable for code standardization. Run it regularly to maintain style consistency and reduce repository drift.
- `tightenco/ziggy` 2.6 and `ziggy-js` 2.6.2 should remain aligned; if using Ziggy for JS routes, confirm matching versions on both sides.
- `laravel/sail` and `laravel/pail` are useful for local development and log handling. Verify that environment dependencies (Docker, etc.) match if the team uses containers.

## Quick Recommendations

- Commit `composer.lock` and the npm lockfile (`package-lock.json` or `pnpm-lock.yaml`) to lock down reproducible versions.
- Update dependencies carefully, especially `laravel/framework`, `vite`, `tailwindcss`, and `vue`, as each may require configuration changes.
- Document installation steps and required runtime versions in `README.md` (PHP 8.2, Node 18+/20+ compatible with Vite 8).
- If possible, add a `php -v` and `node -v` check in CI to ensure environment compatibility.
