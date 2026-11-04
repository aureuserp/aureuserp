# CRM

Customer relationship management for AureusERP: capture leads, move opportunities through a sales pipeline, plan activities, create quotations and follow everything in reports.

## Features

- **Leads**: Kanban board and list, with Convert to Opportunity and Lost.
- **Pipeline**: opportunities on a Kanban board by stage, with Won and Lost, expected revenue, probability, priority and tags.
- **Activities**: schedule, reschedule and mark activities as done (Kanban, list and graph).
- **Quotations**: the Sales plugin's quotations, opened from CRM.
- **Teams and Contacts**: each sales team's pipeline, and the shared contacts.
- **Reporting**: Forecast, Pipeline, Leads and Activities reports, with Graph, Summary and List views (Forecast also has a Kanban view).
- **Import and Export**: CSV import for leads and opportunities; CSV and Excel export for leads, opportunities, quotations and the Forecast report.
- **Kanban columns**: add, rename or delete stages (and activity types) directly from the board.
- **Languages**: English, Arabic, Spanish, French and Brazilian Portuguese.

## Requirements

The `contacts` and `sales` plugins. The installer installs them if needed.

## Installation

```bash
composer dump-autoload
php artisan crm:install --no-interaction
```

You can also install it from **Plugins** in the admin panel. The installer creates the CRM tables, adds the default stages (New, Qualified, Proposition, Won), lost reasons and activity types (Email, Call, Meeting, To-Do), and gives the admin role all CRM permissions.

To uninstall:

```bash
php artisan crm:uninstall
```

This removes the CRM tables and data.

## Navigation

| Menu | Pages |
|---|---|
| **Sales** | My Pipeline, My Activities, My Quotations, Teams, Contacts |
| **Leads** | Leads |
| **Reporting** | Forecast, Pipeline, Leads, Activities |
| **Configuration** | Sales Teams, Stages, Tags, Lost Reasons, Activity Types, Activity Plans, Users, Languages, Companies |

## Permissions

Every CRM screen has its own permissions under **CRM** in the role editor (Settings → Roles), for example `view_any_crm_opportunity` or `create_crm_lead`. Reports only need their view permission, for example `view_any_crm_forecast`.

After changing `config/filament-shield.php`, run `php artisan crm:install` again to update the permissions.

## Import and export

- **Export** (list pages and Kanban boards) uses the current filters. Choose the columns, then download the file from the notification bell when it's ready.
- **Import** takes a CSV with the same columns as the export. A row with an ID updates that record; a row without one creates a new record. Empty cells leave values unchanged. Rows that can't be imported are listed in a downloadable CSV with the reason.

Imports and exports run as background jobs, so a queue worker must be running:

```bash
php artisan queue:work
```

After updating the code, restart the worker (`php artisan queue:restart`, then start it again).

## Tests

The tests run on a separate database (`erp_testing`, configured in `.env.testing`), which is rebuilt on every run.

```bash
php artisan test --testsuite=CrmFeature --compact
```

> Don't run `php artisan config:cache` on a development machine. With a cached config, Laravel ignores `.env.testing`, and the tests would wipe the main database.

## Frontend assets

CRM ships its own stylesheet for the Kanban boards. After changing the views or CSS, rebuild it:

```bash
cd plugins/webkul/crm && npm run build
php artisan filament:assets
```
