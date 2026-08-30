# Phase 3 — Reports & Statements

> **Objective:** Build the six required reports/statements so managers and accountants can answer “who do we owe / who owes us / what’s in stock / what moved” without Excel. All reports derive from **authoritative AureusERP tables**, not denormalized copies.

> **Skills:** `laravel-expert` + `laravel-development` (query design, no N+1) → `test-driven-development` (reconciliation tests) → `requesting-code-review` + `github-pr-workflow` (gate)

## Preconditions

- Phase 2 merged; supplies confirm→inventory/accounting works; seed data exists (supplies, sales, payments, expenses).

## Scope (PRD §17–22 + §6)

| Report | PRD | Source of truth |
|---|---|---|
| Supplier Statement | §17 | `supplies` + `accounting` payments/journals |
| Customer Statement | §18 | `sales_orders` + accounting receipts |
| Supply Report | §19 | `supplies` |
| Sales Report | §21 | `sales_orders` |
| Inventory Report | §20 | `inventories_product_quantities` / `moves` |
| Cash Movement | §22 | `accounting` journals + payments + expenses |
| Dashboard KPIs | §6 | Aggregates over the above |

## Tasks

### T3.1 — Report Infrastructure

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` | `plugins/webkul/supplies/src/Support/ReportQuery.php`, `src/Filament/Pages/Reports.php` or cluster | `laravel-expert` (query scopes, eager loading), `plan` (if new) |

- Decide report delivery: Filament **Pages** with `Filters` + `Tables` (read-only) + Excel export via `maatwebsite/excel` (already required). Avoid custom API until needed.
- Shared `ReportQuery` helper: date range, supplier/product/warehouse scoping, eager loading, pagination, indexed queries.
- All queries must be **server-side filtered**; no loading thousands of rows into memory.
- **Skill `laravel-expert`:** query scopes for reusable filters, `with(['supplier','product'])`, pagination, no raw queries.

### T3.2 — Supplier Statement (PRD §17)

| Owner | Files | Skills |
|---|---|---|
| `accounting-integration-engineer` | `src/Filament/Pages/SupplierStatement.php`, `src/Services/StatementService.php` | `laravel-expert` + `test-driven-development` |

- Filters: supplier (searchable Partner), date from/to.
- Columns: date, reference, type (Supply / Payment), supply amount, payment, balance (running).
- Summary: total supplies, total paid, current balance.
- Balance **must reconcile** with accounting/payments — Pest proves the statement total equals `sum(supplies.total) - sum(payments)` for the supplier.
- Arabic labels, EGP formatting, printable/exportable.
- **Skill `test-driven-development`:** write `SupplierStatementReconciliationTest` FIRST: seed known supplies/payments → assert statement.balance == accounting query → GREEN.

### T3.3 — Customer Statement (PRD §18)

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` | `src/Filament/Pages/CustomerStatement.php` | `laravel-expert` + `test-driven-development` |

- Same pattern as supplier statement but over `sales_orders` + customer receipts.
- Filters: customer, date range; columns: date, ref, type (Sale/Payment), sale amount, payment, balance.

### T3.4 — Supply Report (PRD §19)

| Owner | Files | Skills |
|---|---|---|
| `supply-domain-engineer` | `src/Filament/Pages/SupplyReport.php` | `laravel-expert` + `test-driven-development` |

- Filters: date from/to, supplier, product, warehouse, status.
- Columns: supply number, date, supplier, material, net KG, unit price, total, paid, remaining.
- Summary: total KG, total value, total paid, total remaining (aggregates over filtered set).
- Export to Excel.
- **Skill `test-driven-development`:** `SupplyReportAggregatesTest` (filtered aggregates correct).

### T3.5 — Inventory Report (PRD §20)

| Owner | Files | Skills |
|---|---|---|
| `inventory-integration-engineer` | `src/Filament/Pages/InventoryReport.php` | `laravel-expert` |

- Source: `ProductQuantity` / `inventories_product_quantities` + `Move` for movements — **do not read `supplies` stock**.
- Filters: warehouse, product; columns: product, warehouse, current quantity, stock value where reliable.
- Show per-warehouse breakdown; link to `ProductQuantity` detail.
- **Skill `systematic-debugging`:** if `ProductQuantity` diverges from `supplies.net`, trace data flow (Tight loop: `php artisan test --filter=InventoryReport -v`).

### T3.6 — Sales Report (PRD §21)

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` | `src/Filament/Pages/SalesReport.php` | `laravel-expert` + `test-driven-development` |

- Filters: date range, customer, product, warehouse.
- Columns: quantity, sales value, paid, outstanding; aggregates.

### T3.7 — Cash Movement Report (PRD §22)

| Owner | Files | Skills |
|---|---|---|
| `accounting-integration-engineer` | `src/Filament/Pages/CashMovementReport.php` | `laravel-expert` |

- Show: opening balance (where supported), cash received, cash paid, expenses, supplier payments, customer collections, closing balance.
- Prefer `accounting` journals/payments as source.
- If opening/closing balance not supported by AureusERP config, document and show movement-only.

### T3.8 — Dashboard KPIs (PRD §6)

| Owner | Files | Skills |
|---|---|---|
| `filament-ui-engineer` | `src/Filament/Widgets/{StatsOverview,RecentSupplies,RecentSales}.php` | `laravel-expert` (efficient aggregates) |

- KPIs (today + totals): total stock qty, total supplier payable, total customer receivable, cash/bank balance, today's supplies/sales/cash in/out.
- Optional month aggregates if cheap.
- Keep queries lightweight — cached or aggregated, not full table scans. Use `flowframe/laravel-trend` if helpful.
- Arabic, RTL-safe widgets.
- **Skill `simplify-code` (Efficiency reviewer):** check for N+1/hot-path bloat after implementation.

### T3.9 — Exports & Printing

| Owner | Files | Skills |
|---|---|---|
| `filament-ui-engineer` + `localization-rtl-engineer` | `src/Filament/Exports/*`, `resources/views/reports/*.blade.php` | `laravel-expert` |

- Excel export for every report (via `maatwebsite/excel`).
- Print-friendly views with Arabic + EGP; dompdf for PDF if needed.

## Acceptance Criteria

- [ ] Supplier statement filters + running balance + summary reconciles with accounting (TDD proven).
- [ ] Customer statement similarly reconciles.
- [ ] Supply report filters + aggregates correct (Pest proves totals).
- [ ] Inventory report pulls from `ProductQuantity`/moves, not `supplies`.
- [ ] Sales report filters + aggregates correct.
- [ ] Cash movement shows received/paid/expenses/payments/collections.
- [ ] Dashboard KPIs load < 500ms on seed data (no N+1, no full scan).
- [ ] Every report paginated, server-side filtered, searchable selects, Arabic labels.

## Exit Criteria (gate to Phase 4)

- All Acceptance Criteria + Pest feature tests for each report passing.
- Reconciliation test: seed → run reports → totals match direct accounting/inventory queries.
- **Skills gate:** `test-driven-development` (reconciliation tests green), `requesting-code-review` passed, `simplify-code` Efficiency reviewer checked KPIs.

## Risks

- Accounting cash/bank opening balance may not be available — define fallback early.
- Large date ranges could be slow — ensure indexes on `supplies.date`, FKs, and accounting date columns.

## Decisions Log

| Date | Decision | Owner | Rationale |
|---|---|---|---|
|  |  |  |  |

## Handoff Checklist → Phase 4

- [ ] Reports demoed in Arabic with seed data.
- [ ] `requesting-code-review` passed on `phase/3-reports` PR.
- [ ] `phase/3-reports` merged via `github-pr-workflow`; `phase/4-ux` branched (may run in parallel with Phase 4).
