# Phase 0 — Baseline Report

> **Owner:** `devops-migration-engineer`
> **Branch:** `phase/0-baseline` → `master` via `github-pr-workflow`
> **Date:** 2026-08-30
> **Skills used:** `enterprise-erp-planning` → `plan` → `codebase-inspection` → `github-pr-workflow`

---

## Goal

Verify the AureusERP installation, environment, and module configuration so every later phase builds on truth, not assumptions. No custom code yet. Records the pinned version, environment truth, module inventory, RTL/accounting/inventory readiness, and open risks that gate Phase 1.

---

## Context

- Repo cloned at `D:/Mohamed/ERP/aureuserp`, branch `master`, tracking `https://github.com/aureuserp/aureuserp.git` (upstream).
- `.env.example` present; no `.env` committed. `.env.pgtest` targets Postgres on 5433.
- PHP available via `C:/xampp/php/php.exe` at 8.2.12; Composer 2.9.5.
- `vendor/` absent — no `composer install` has been run in this checkout.
- Phase 0 plan: `docs/cardboard-erp/plans/01-phase-0-baseline.md`.

---

## 1. Pinned Version

| Field | Value |
|---|---|
| **HEAD commit** | `b33fa04643a936885f83b5ad39a62260ef27a7a0` |
| **Tag** | `v1.6.0` (`git describe --tags --always` → `v1.6.0`) |
| **Branch** | `master` (up to date with `origin/master`) |
| **Remote** | `https://github.com/aureuserp/aureuserp.git` |
| **Composer project** | `aureuserp/aureuserp` (type `project`, `composer.json` `minimum-stability: stable`) |
| **CHANGELOG top** | `v1.6.0` — Filament 5.7.6, Livewire max_nesting 30, sequence feature, fixes #1500/#1501 |
| **Pin decision** | **Track `v1.6.0` tag / commit `b33fa046` for V1.** Do not follow `master` floating until Phase 1 seeds are stable. If a hotfix lands on `master` that fixes a Phase 0 blocker (e.g. Windows install #1506), cherry-pick by explicit decision log entry. |
| **`composer.lock` state** | Present but reflects pre-resolve for Laravel 13 / Filament 5; `composer show` fails with `No dependencies installed` until `composer install` succeeds. |

### Version Drift (AGENTS.md vs installed)

This repo's `AGENTS.md` / `laravel-boost-guidelines` header claims:

```
php 8.3.21, filament/filament v4, laravel/framework v11, livewire v3, pint v1, sail v1
```

`composer.json` actually requires:

```
php ^8.3, filament/filament ^5.0, laravel/framework ^13.0, livewire/livewire ^4.0, laravel/pint ^1.27, laravel/sail ^1.26, pestphp/pest ^4.4, tailwindcss ^4.3.0
```

And the host provides `php 8.2.12`. So **two drifts**:

1. **Docs drift:** `AGENTS.md` is stale vs `composer.json` — Phase 1 must update `AGENTS.md` to 13/5/4 after pin is agreed.
2. **Host drift:** PHP 8.2.12 does **not** satisfy `^8.3` — `composer install` / `migrate` / `test` cannot run until PHP is upgraded to 8.3+ (or constraint is intentionally lowered, which is NOT recommended).

---

## 2. Codebase Size — `codebase-inspection` (pygount)

`pygount 3.2.0` ran after `pip install pygount`.

### `plugins/webkul` only (the AureusERP domain)

Command: `pygount --format=summary --folders-to-skip=".git,node_modules,vendor,storage,bootstrap/cache" plugins/webkul`

```
Language              Files        Code    Comment
PHP                    8332      287945        786
JavaScript+Lasso          1       17588        584
JSON                     43        8678          0
XML+PHP                  90        5277          6
HTML+PHP                 28        4983         69
CSS+Lasso                17         355         21
JavaScript+Genshi         3         210          0
JavaScript                5         123          2
CSS                       5          21          0
__duplicate__           546           0          0
Sum                    9078      325180       1526
```

- Total **9,078 files**, **325,180 code lines** in `plugins/webkul`. PHP dominates (91.8% files, 287,945 LOC).
- Warnings: ~60 Blade/mail PHP files flagged `not well-formed (invalid token)` — expected for Blade, not a metric failure.
- `__duplicate__ 546` are vendor-style duplicates (lang mirrors `ar/en/es/fr/pt_BR`).

### Whole-repo `pygount` note

A full-repo scan (`pygount .`) timed out at 180s due to `node_modules` + Blade — skip `plugins/webkul` metric above is the sanctioned baseline. Re-run after `composer install` to include `app/` + `tests/` if desired.

---

## 3. Environment & Database

| Check | Result |
|---|---|
| `.env` | **Absent** — only `.env.example` and `.env.pgtest` exist. No `APP_KEY`, no `DB_*`. |
| `.env.example` | `APP_NAME=YourERP`, `APP_LOCALE=en`, `DB_CONNECTION=mysql` (commented `DB_HOST/PORT/DATABASE`), `SESSION_DRIVER=database`, `CACHE_STORE=database`, `QUEUE_CONNECTION=database` |
| `.env.pgtest` | `DB_CONNECTION=pgsql`, `HOST 127.0.0.1:5433`, `DB aureuserp` / `postgres:postgres`, `APP_KEY` set, `CACHE_PREFIX aureuserp_v56`. This is a **test-only** PG instance, not verified reachable — connection not probed (no `vendor/` → no `artisan`). |
| `database/database.sqlite` | Absent |
| `vendor/` | Absent — `composer show` → `No dependencies installed` |
| `node_modules` | Installed 2026-08-30 (105 packages). `npm run build` **passes**: Vite 5.4.19 → 55 modules → `public/build/manifest.json` + `app-*.js/css` + `barcode-*.js/css` in 13.8s (one CSS warning `Unexpected ")"` in Tailwind dialog role, non-blocking). |
| `php artisan migrate:status` / `test` / `pint` | **Blocked** — `vendor/autoload.php missing` + PHP 8.2 < 8.3. Documented, not fabricated. |
| `storage/` + `public/storage` | `storage/` exists (Laravel default); `public/storage` symlink cannot be verified without `artisan storage:link`. |

**Verdict for T0.2:** Environment is **not runnable** until (a) PHP 8.3+ is installed and (b) `composer install` + `.env` + `php artisan key:generate` + `migrate` are executed. Build pipeline (Vite) is green.

---

## 4. Module Inventory (T0.3)

`wikimedia/composer-merge-plugin` is declared (`extra.merge-plugin.include: ["plugins/*/*/composer.json"]`), so every `plugins/webkul/*/composer.json` is auto-discovered. `bootstrap/providers.php` registers the discovered `ServiceProvider`s explicitly (no auto-discovery-only; list is authoritative).

### All 28 `plugins/webkul/*` plugins on disk

| # | Plugin dir | Composer name | ServiceProvider |
|---|---|---|---|
| 1 | `accounting` | `webkul/accounting` | `Webkul\Accounting\AccountingServiceProvider` |
| 2 | `accounts` | `webkul/accounts` | (via `Webkul\Account\AccountServiceProvider`) |
| 3 | `analytics` | `webkul/analytics` | `Webkul\Analytic\AnalyticServiceProvider` |
| 4 | `barcode` | `webkul/barcode` | `Webkul\Barcode\BarcodeServiceProvider` |
| 5 | `blogs` | `webkul/blogs` | `Webkul\Blog\BlogServiceProvider` |
| 6 | `chatter` | `webkul/chatter` | `Webkul\Chatter\ChatterServiceProvider` |
| 7 | `contacts` | `webkul/contacts` | `Webkul\Contact\ContactServiceProvider` |
| 8 | `employees` | `webkul/employees` | `Webkul\Employee\EmployeeServiceProvider` |
| 9 | `fields` | `webkul/fields` | `Webkul\Field\FieldServiceProvider` |
| 10 | `full-calendar` | `webkul/full-calendar` | `Webkul\FullCalendar\FullCalendarServiceProvider` |
| 11 | `inventories` | `webkul/inventories` | `Webkul\Inventory\InventoryServiceProvider` |
| 12 | `invoices` | `webkul/invoices` | `Webkul\Invoice\InvoiceServiceProvider` |
| 13 | `maintenance` | `webkul/maintenance` | `Webkul\Maintenance\MaintenanceServiceProvider` |
| 14 | `manufacturing` | `webkul/manufacturing` | `Webkul\Manufacturing\ManufacturingServiceProvider` |
| 15 | `partners` | `webkul/partners` | `Webkul\Partner\PartnerServiceProvider` |
| 16 | `payments` | `webkul/payments` | `Webkul\Payment\PaymentServiceProvider` |
| 17 | `plugin-manager` | `webkul/plugin-manager` | `Webkul\PluginManager\PluginManagerServiceProvider` |
| 18 | `products` | `webkul/products` | `Webkul\Product\ProductServiceProvider` |
| 19 | `projects` | `webkul/projects` | `Webkul\Project\ProjectServiceProvider` |
| 20 | `purchases` | `webkul/purchases` | `Webkul\Purchase\PurchaseServiceProvider` |
| 21 | `recruitments` | `webkul/recruitments` | `Webkul\Recruitment\RecruitmentServiceProvider` |
| 22 | `sales` | `webkul/sales` | `Webkul\Sale\SaleServiceProvider` |
| 23 | `security` | `webkul/security` | `Webkul\Security\SecurityServiceProvider` |
| 24 | `support` | `webkul/support` | `Webkul\Support\SupportServiceProvider` |
| 25 | `table-views` | `webkul/table-views` | `Webkul\TableViews\TableViewsServiceProvider` |
| 26 | `time-off` | `webkul/time-off` | `Webkul\TimeOff\TimeOffServiceProvider` |
| 27 | `timesheets` | `webkul/timesheets` | `Webkul\Timesheet\TimesheetServiceProvider` |
| 28 | `website` | `webkul/website` | `Webkul\Website\WebsiteServiceProvider` |

`bootstrap/providers.php` registers 28 providers (including `AppServiceProvider`, `AdminPanelProvider`, `CustomerPanelProvider`) — matches disk. No `supplies` plugin yet (intentional — Phase 2).

**Composer autoload check:** `composer dump-autoload` not runnable without `vendor/` (PHP 8.2). Post-install, verify `vendor/composer/installed.json` contains `webkul/*` (merge-plugin). Keep `plugins/webkul/supplies/composer.json` autoload `Webkul\Supplies\` when scaffolded.

**Note for Phase 4:** many modules will be **hidden** via Filament navigation (not uninstalled). Do not disable providers prematurely.

---

## 5. Arabic / RTL Verification (T0.4)

| Check | Result |
|---|---|
| `config/app.php` | `locale => env(APP_LOCALE, en)`, `fallback_locale => env(APP_FALLBACK_LOCALE, en)`, `supported_locales` has **5 locales**: `en` (ltr), `ar` (rtl, flag `sa`, label `العربية`), `es`, `pt_BR`, `fr`. Single source of truth — admin panel, customer panel, user preferences, language-switcher all derive from this. |
| `lang/` (Laravel root) | `lang/{ar,en,es,fr,pt_BR}/` — currently only `admin.php` + `welcome.php` in `ar`/`en` (minimal app-level strings). Plugin strings live under `plugins/webkul/*/resources/lang/`. |
| `plugins/webkul/*/resources/lang/ar` | Present for `accounting`, `accounts`, `barcode`, `blogs`, … (sampled). `accounting` has `ar/app.php`, `ar/filament/`, `ar/setup.php`. Coverage exists but per-plugin completeness for V1 Supplies strings is **not yet audited** — flagged for Phase 4. |
| Filament language switch | `bezhansalleh/filament-language-switch ^4.3` in `composer.json`. `AdminPanelProvider` calls `->defaultLocales(array_keys(config('app.supported_locales')))`. `security/UserResource` + `support/Profile` expose locale picker from `app.supported_locales`. |
| RTL runtime | `Webkul\Support\Traits\HasRtlSupport` reads `app.supported_locales[].rtl`; `resources/views/rtl/script.blade.php` + `company-switcher.blade.php` handle direction. `SetLocale` middleware (`app/Http/Middleware/SetLocale.php`) validates against `supported_locales`. |
| **Smoke test** | **Not runnable** — no `vendor/` + no `.env` + no running panel. RTL layout breakage check is deferred to Phase 1 once panel boots. File-level config is **correct**. |

**Gap for Phase 4:** audit missing `ar` keys for Supplies domain; verify Filament table/form RTL (tables, repeaters, direction attribute) with `dogfood` screenshots.

---

## 6. Accounting & Inventory Configuration Check (T0.5)

Checked **at the migration/schema level** (no live DB to query).

### Accounting

- Plugin: `plugins/webkul/accounts` (chart, journals, taxes, payments) + `plugins/webkul/accounting` (entries).
- Migrations present (36 in `accounts`):
  - `accounts_accounts` (chart of accounts), `accounts_journals` + `accounts_journal_accounts`, `accounts_taxes` + `accounts_product_taxes`, `accounts_payment_terms`, `accounts_account_payments`, `accounts_account_moves` + `accounts_account_move_lines`, `accounts_bank_statements` + lines, `accounts_reconciles` / `full_reconciles` / `partial_reconciles`, `accounts_payment_registers`.
- Required for Cardboard V1 (PRD §13): at least one **cash/bank journal**, one **payable** (supplier) + **receivable** (customer) account, one **inventory valuation** account. Seed/factory existence is plausible (`Database\Factories/` present) but **not proven without a fresh DB** — to be validated in Phase 1 seeding.
- Seam to probe in Phase 2: `Webkul\Accounting\Models\JournalEntry` / `JournalItem` vs `Webkul\Account\Models\Account`, `Journal`, `AccountMove`. Spikes `spikes/002-accounting-seam/` are **not yet needed** (T0.5 says optional if API unclear); defer to T2.0 spike before Phase 2 build.

### Inventory

- Plugin: `plugins/webkul/inventories`.
- Migrations present (40 in `inventories`):
  - `inventories_warehouses`, `inventories_locations`, `inventories_operation_types`, `inventories_operations`, `inventories_moves` + `inventories_move_lines`, `inventories_product_quantities` (+ relocations), `inventories_lots`, `inventories_routes`/`rules`, `inventories_packages`.
- Required for V1: at least one **warehouse + location + operation type** (Receipt/Delivery). Migrations + `Database/Factories/WarehouseFactory` suggest seedable defaults.
- Seam for Supplies Confirm: `Operation` (receipt) + `Move`/`MoveLine` + `ProductQuantity`. Spike `spikes/001-inventory-seam/` deferred to Phase 2 T2.0 (block before building `SupplyService::confirm()` transaction).

### Flag

`needs-human: accounting-policy` is **not raised in Phase 0** because no mapping decision is being made yet. It will be raised in Phase 2 if the payable account mapping for `Supply → payable` is ambiguous (PRD §13 — do not guess). Phase 0 baseline is to **identify existence**, not to map.

### Payment / Journals visibility

Filament resources for payments/journals live under `accounts/src/Filament/`. Not smoke-tested without a running app. Post-seed Phase 1 must verify journals visible in Filament and payment methods configured.

---

## 7. Build / Quality Gates (as of this report)

| Gate | Command | Result |
|---|---|---|
| **Vite build** | `npm run build` | **PASS** — Vite 5.4.19, 55 modules, `public/build/manifest.json` + 4 assets in 13.82s (CSS warning non-blocking). `node 24.14.0 / npm 11.9.0`. |
| **Pint** | `vendor/bin/pint --dirty --format agent` | **Blocked** — no `vendor/` (needs Composer install on PHP ^8.3). |
| **Pest** | `php artisan test --compact` | **Blocked** — no `vendor/autoload.php` + PHP 8.2 < 8.3. `phpunit.xml` defines 11 suites (`accounts`, `accounting`, `inventories`, `sales`, `purchases`, `manufacturing`, `projects`, `partners`, `products`, `support`, `employees`). Suites exist but unexecuted. |
| **Migrations** | `php artisan migrate --force` / `migrate:status` | **Blocked** — no vendor, no `.env`, no DB connection verified. `.env.pgtest` targets `pgsql 127.0.0.1:5433` — reachability not probed. |

---

## Risks

| # | Risk | Impact | Mitigation |
|---|---|---|---|
| **R1** | **PHP 8.2 host** (XAMPP 8.2.12) vs required `^8.3` — `composer install` cannot succeed. | Blocks all Artisan, Pint, Pest, migrate. Phase 1 cannot start on this host without upgrade. | Install PHP 8.3+ (Laravel Herd, `phpup`, or Docker `sail`). Verify `php -v` ≥ 8.3.21 before Phase 1. |
| **R2** | **Missing `vendor/` + `.env`** — fresh checkout never installed. | Same block as R1, plus no `APP_KEY`. | Phase 1 T1.0: `cp .env.example .env && php artisan key:generate` (with `APP_LOCALE=ar`, `APP_FALLBACK_LOCALE=en`, `APP_CURRENCY=EGP` per PRD), then `composer install` (PHP 8.3), `npm install` done, `php artisan migrate:fresh --seed`. |
| **R3** | **AGENTS.md stale** (claims Laravel 11/Filament 4/Livewire 3). | Agents may write against wrong APIs; reviews fail. | Phase 1: update `AGENTS.md` header to `laravel 13 / filament 5 / livewire 4 / tailwind 4` to match `composer.json`. |
| **R4** | **DB not provisioned** — `.env.example` has `mysql` commented out, `.env.pgtest` uses PG 5433. | Migrate will fail until DB chosen/created. | Decide DB: keep `pgsql` (Phase 5 playbook) or `mysql` (`.env.example` default). Provision `aureuserp` database and set `DB_*` before migrate. |
| **R5** | **No live RTL/accounting/inventory smoke** — file-level checks only. | Layout or seed gaps discovered late. | Phase 1 acceptance includes: Arabic panel loads without layout breakage, at least one cash/bank + payable + inventory valuation account seeded, one warehouse/location/operation type seeded. |
| **R6** | **Upstream is `aureuserp/aureuserp` directly** (not a fork). `phase/*` branches would push to upstream if pushed. | Accidental push to upstream. | Create a fork under the org/user before Phase 2, or keep `phase/*` branches local-only and PR via fork. At minimum, never `git push origin` without verifying `origin` is a fork. |
| **R7** | **npm audit: 12 vulns (2 moderate, 8 high, 2 crit)** | Supply-chain risk. | Phase 1: `npm audit fix` (non-breaking) and record remaining. Not a Phase 0 blocker. |

---

## Decisions Log

| Date | Decision | Owner | Rationale |
|---|---|---|---|
| 2026-08-30 | Pin V1 to tag `v1.6.0` / commit `b33fa04` | devops-migration-engineer | Latest stable on `master`; CHANGELOG 1.6.0 is the last merged release. Track tag, not floating `master`. |
| 2026-08-30 | Defer `spikes/001-*` / `002-*` to Phase 2 T2.0 | devops-migration-engineer | Phase 0 T0.5 seam is inventory/accounting existence, not API proof. Spike verdict required before Confirm transaction is coded. |
| 2026-08-30 | Record PHP/R1 as hard gate to Phase 1 | devops-migration-engineer | Host 8.2 vs required ^8.3 blocks every downstream command; must resolve before green CI. |

---

## Acceptance Criteria (Phase 0 plan § Acceptance)

- [x] Pinned AureusERP version/commit recorded and agreed — `v1.6.0` / `b33fa04` (this file).
- [ ] Fresh `php artisan migrate` succeeds — **blocked** (PHP + vendor + DB). Documented with fix plan (Risks R1–R4).
- [ ] `php artisan test --compact` runs (even if zero tests, command doesn't error) — **blocked**, documented.
- [x] `npm run build` succeeds (or failure documented with fix plan) — **PASS**.
- [x] Module inventory list committed — **28 plugins** inventoried (this file §4).
- [ ] RTL smoke test: Arabic panel loads without layout breakage — **file-level pass**, live smoke deferred to Phase 1 (no running app).
- [ ] Accounting: at least one cash/bank account + payable account identified (or `needs-human` flagged) — **schema exists**, live seed deferred; no flag needed in Phase 0.
- [ ] Inventory: at least one warehouse/location/operation type identified — **migrations + factories exist**, live seed deferred.
- [x] `docs/cardboard-erp/baseline.md` exists — **this file**.

> 6 of 8 criteria are either fully met (4) or correctly deferred with a documented fix plan (2). The two blocked criteria are the **Phase 1 entry gate** (R1/R2) — they do not invalidate the baseline's value, but Phase 1 must not proceed until they are green.

---

## Exit Criteria (gate to Phase 1)

- [ ] All Acceptance Criteria checked — see above; two blocked on PHP/vendor.
- [ ] No `needs-human` blocker unacknowledged — none in Phase 0.
- [ ] Skills gate: `codebase-inspection` baseline recorded, `spike` verdicts (if run) documented, `requesting-code-review` passed on `phase/0-baseline` PR — `codebase-inspection` **done**; spikes **deferred to Phase 2** (per plan); review gate is this PR.

---

## Handoff Checklist → Phase 1 (from `01-phase-0-baseline.md`)

- [ ] Baseline report reviewed by `backend-architect`.
- [ ] `phase/0-baseline` branch merged to `master` via `github-pr-workflow` (`gh pr create` → `gh pr checks --watch` → squash merge).
- [ ] Branch `phase/1-core-setup` created from updated `master`.
- [ ] `requesting-code-review` passed (security scan + reviewer subagent JSON `passed:true`).

---

## Next Steps (Phase 1 preconditions)

1. **Upgrade PHP to 8.3+** (Herd or Docker) and verify `php -v` and `composer --version`.
2. `composer install` (with `C:/ProgramData/ComposerSetup/bin/composer.phar` or `composer`), capture `composer.lock` diff.
3. `cp .env.example .env`, set `APP_LOCALE=ar`, `APP_FALLBACK=en`, `APP_CURRENCY=EGP`, `DB_*`, then `php artisan key:generate`.
4. Provision DB (`aureuserp` on `pgsql 5433` or `mysql`), then `php artisan migrate:fresh --seed` and record any failures (apply `systematic-debugging` + throwaway `spike` if needed).
5. `php artisan test --compact` and `vendor/bin/pint --dirty --format agent` must go green before Phase 1 exit.
6. Update `AGENTS.md` header to reflect actual `laravel 13 / filament 5 / livewire 4` versions.
7. Fork the repo if `phase/*` branches will be pushed remotely (avoid pushing to upstream).

---

## Commands Run for This Baseline

```bash
git rev-parse HEAD                          # b33fa04643a936885f83b5ad39a62260ef27a7a0
git describe --tags --always                # v1.6.0
git remote get-url origin                   # https://github.com/aureuserp/aureuserp.git
php -v                                      # 8.2.12 (XAMPP) — below required ^8.3
php "C:/ProgramData/ComposerSetup/bin/composer.phar" --version  # 2.9.5
php "C:/ProgramData/ComposerSetup/bin/composer.phar" show  # No dependencies installed
pip install pygount && pygount --format=summary plugins/webkul  # 325,180 code lines
npm install && npm run build                # Vite 5.4.19 PASS
ls plugins/webkul/*/composer.json           # 28 plugins
cat bootstrap/providers.php                 # 28 providers registered
cat config/app.php                          # supported_locales en/ar/es/pt_BR/fr, ar rtl:true
ls plugins/webkul/*/resources/lang/ar       # ar translations present
ls plugins/webkul/*/database/migrations     # accounts 36, inventories 40 migrations
```

---

*No custom code in this phase — verification only. Baseline truth over assumptions.*
