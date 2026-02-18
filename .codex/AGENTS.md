# AureusERP Agent Notes

## Plugin Root
- All domain plugins live under `plugins/webkul`.
- Most plugins are Laravel package-style modules with `composer.json` + `src/`.

## Available Plugins
- `accounting`
- `accounts`
- `analytics`
- `blogs`
- `chatter`
- `contacts`
- `docker-timer`
- `employees`
- `fields`
- `full-calendar`
- `inventories`
- `invoices`
- `partners`
- `payments`
- `plugin-manager`
- `products`
- `projects`
- `purchases`
- `recruitments`
- `sales`
- `security`
- `support`
- `table-views`
- `time-off`
- `timesheets`
- `website`

## Common Plugin Structure
- `src/`: PHP source (models, services, providers, Filament resources/pages).
- `database/`: migrations/seeders/factories when present.
- `resources/`: blade views, frontend assets, translation files.
- `routes/`: only some plugins define this folder; check before adding routes.
- `config/`: plugin-specific config in many plugins.

## Routing Snapshot
- API routes currently present in:
  - `plugins/webkul/accounts/routes/api.php`
  - `plugins/webkul/partners/routes/api.php`
  - `plugins/webkul/products/routes/api.php`
  - `plugins/webkul/sales/routes/api.php`
  - `plugins/webkul/security/routes/api.php`
  - `plugins/webkul/support/routes/api.php`
- Web routes currently present in:
  - `plugins/webkul/purchases/routes/web.php`
  - `plugins/webkul/security/routes/web.php`

## Notable Exceptions
- Some plugins omit `database/` or `resources/` (do not assume both exist).
- `timesheets` and `contacts` currently have no `database/` folder.

## Agent Working Rules For This Repo
- Prefer implementing feature work inside the owning plugin under `plugins/webkul/<plugin>`.
- Reuse existing module patterns from sibling plugins before creating new structures.
- When adding endpoints, verify whether the plugin already exposes `routes/api.php` or needs route registration updates.
- Keep changes plugin-local unless cross-plugin coupling is required.
