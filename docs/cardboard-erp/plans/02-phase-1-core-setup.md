# Phase 1 — Core Setup

> **Objective:** Configure company, currency, warehouses, accounts, users/roles, and products using **existing AureusERP** — no custom business code. At the end, an employee can log in and see a coherent, Arabic-first skeleton.

> **Skills:** `enterprise-erp-planning` → `laravel-expert` + `laravel-development` (Laravel standards) → `github-pr-workflow` → `requesting-code-review`

## Preconditions

- Phase 0 exit criteria met; `docs/cardboard-erp/baseline.md` exists; version pinned.

## Tasks

### T1.1 — Company & Currency

| Owner | Where | Skills |
|---|---|---|
| `devops-migration-engineer` / `backend-architect` | `Webkul\Support\Models\Company`, Filament Settings, `config/app.php` | `laravel-expert` (config, Settings) |

- Set company name, address, tax ID, logo.
- Set base currency to **EGP** (ensure `Currency` / `Account` models + Filament clusters under `accounting`/`purchases` expose EGP correctly).
- Verify `SequenceService` numbering respects company.
- **Skill `laravel-expert`:** use `config/*.php` + `env()` only in config, strict types, proper casting.

### T1.2 — Warehouses & Locations

| Owner | Where | Skills |
|---|---|---|
| `inventory-integration-engineer` | `Webkul\Inventory\Models\Warehouse`, `Location`, Filament `Configurations/Resources/LocationResource` | `laravel-expert` (Eloquent, relationships) |

- Create **Main Warehouse** (PRD §9) with at least one stock location.
- Confirm Reception/Delivery `OperationType`s exist and are linked to the warehouse.
- Seed via migration/seeder, not manual clicks alone (so fresh envs are reproducible).
- **Skill `laravel-expert`:** use factories, `HasFactory`, proper `fillable`/`casts`, `BelongsTo` relationships.

### T1.3 — Chart of Accounts & Payment Methods

| Owner | Where | Skills |
|---|---|---|
| `accounting-integration-engineer` | `Webkul\Accounting` / `Webkul\Account`, Filament `Accounting` clusters | `laravel-expert` |

- Ensure accounts exist for: inventory valuation, supplier payable, customer receivable, cash, bank, expenses (at least categories).
- Ensure at least one **Cash** and one **Bank** journal/payment method.
- Document the account mapping that Phase 2 will use (PRD §13) — flag `needs-human` if policy unclear.

### T1.4 — Users & Roles (PRD §5)

| Owner | Where | Skills |
|---|---|---|
| `backend-architect` | `Webkul\Security`, `bezhansalleh/filament-shield` | `laravel-expert` (Policies/Gates, Sanctum) |

- Create Shield roles: **Administrator**, **Warehouse Employee**, **Accountant**, **Manager** with permissions:
  - Warehouse: view products, record supplies/sales if authorized, view stock — no accounting config, no historical edits, no user management.
  - Accountant: payments, expenses, statements, cash/bank — no user management.
  - Manager: dashboards + all reports read-only.
  - Administrator: everything.
- Seed at least one user per role for testing.
- **Skill `laravel-expert`:** Policies/Gates for authorization, never trust hidden UI fields, server-side checks.

### T1.5 — Products / Materials (PRD §8)

| Owner | Where | Skills |
|---|---|---|
| `backend-architect` | `Webkul\Product\Models\Product`, `Webkul\Support\Models\UOM` | `laravel-expert` + `laravel-development` (SOLID, Eloquent) |

- Ensure UOM **KG** exists (reference or bigger unit, `UOMType`).
- Create cardboard products: `Mixed Cardboard`, `White Cardboard`, `Pressed Cardboard`, `Grade A`, `Grade B` (keep catalog small).
- Each product: `name`, `reference`, `uom_id = KG`, `enable_purchase = true`, `enable_sales = true` where appropriate, `cost`/`price` defaults.
- Seed via seeder; ensure `ProductResource` shows them correctly.
- **Skill `laravel-development`:** Repository pattern if needed, Eloquent relationships, proper indexing.

### T1.6 — Partners (Suppliers & Customers) (PRD §7)

| Owner | Where | Skills |
|---|---|---|
| `backend-architect` | `Webkul\Partner\Models\Partner` | `laravel-expert` |

- Seed 3–5 demo partners covering supplier-only, customer-only, and both (via `account_type` / `sub_type`).
- Fields: `name`, `phone`, `mobile`, `street1`, `city`, `country_id`, `tax_id` optional, `notes` via chatter.

### T1.7 — Seeders & Reproducibility

| Owner | Where | Skills |
|---|---|---|
| `devops-migration-engineer` | `database/seeders/`, `plugins/*/database/seeders/` | `laravel-development` (seeders, factories), `github-pr-workflow` |

- Consolidate Phase 1 seeds into a single `CardboardCoreSeeder` or per-plugin seeders that `DatabaseSeeder` calls.
- Verify `php artisan migrate:fresh --seed` produces a usable demo env.
- **Before PR:** run `requesting-code-review` (security scan: no hardcoded passwords in seeders).

## Acceptance Criteria

- [ ] Company + EGP visible in Filament and on printed docs.
- [ ] Main Warehouse + stock location exists; Reception operation works in inventory.
- [ ] Accounts/journals for payable, receivable, cash, bank, inventory exist.
- [ ] Four Shield roles created with correct permission matrix; seeded users can log in.
- [ ] KG UOM + 5 cardboard products exist.
- [ ] Demo suppliers/customers exist as `Partner` records (no custom tables).
- [ ] `php artisan migrate:fresh --seed` reproduces the setup.

## Exit Criteria (gate to Phase 2)

- All Acceptance Criteria + Phase 0 baseline still green.
- Accounting mapping document reviewed (or `needs-human` acknowledged).
- **Skills gate:** `laravel-expert` conventions verified (thin controllers, Policies), `requesting-code-review` passed.

## Risks

- EGP currency missing in AureusERP seed — may need to create `CurrencyResource` entry.
- Shield permission drift — test with real users, not just admin.

## Decisions Log

| Date | Decision | Owner | Rationale |
|---|---|---|---|
|  |  |  |  |

## Handoff Checklist → Phase 2

- [ ] Demo credentials shared (in `.env.example` comments or `docs/cardboard-erp/demo-accounts.md`, never real passwords).
- [ ] `phase/1-core-setup` merged via `github-pr-workflow` (`gh pr checks --watch` green).
- [ ] `supply-domain-engineer` + `inventory-integration-engineer` + `accounting-integration-engineer` briefed on account mapping.
- [ ] `laravel-expert` review passed on seeders/policies.

## Commands

```bash
php artisan make:seeder CardboardCoreSeeder
php artisan migrate:fresh --seed
php artisan shield:generate --all   # if needed
vendor/bin/pint --dirty --format agent
php artisan test --compact
# pre-PR:
# skill_view(name='requesting-code-review') then delegate_task reviewer
```
