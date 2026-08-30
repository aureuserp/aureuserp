# Phase 5 — QA & Hardening

> **Objective:** Prove the system is correct, reconciled, and permission-safe. This phase **gates production**.

> **Skills:** `test-driven-development` (TDD لكل Test) → `systematic-debugging` (عند فشل) → `requesting-code-review` (قبل كل commit) → `simplify-code` (تنظيف) → `dogfood` (QA استكشافي) → `codebase-inspection` (قياس نهائي) → `github-pr-workflow` (merge)

## Preconditions

- Phases 0–4 merged; demo data covers supplies, sales, payments, expenses.

## Tasks

### T5.1 — Unit Tests (PRD §38)

| Owner | Files | Skills |
|---|---|---|
| `qa-testing-engineer` | `plugins/webkul/supplies/tests/Unit/*` | `test-driven-development` (RED→GREEN→REFACTOR) |

- `SupplyCalculationsTest`: `net = gross - tare`, `total = net * price`, `remaining = total - paid`, edge cases (zero, rounding, boundary `paid == total`).
- `SupplyValidationTest`: gross ≤ 0, tare ≥ gross, net ≤ 0, paid > total, missing supplier/product/warehouse.
- `SupplyStatusTransitionsTest`: draft→confirmed→cancelled, illegal transitions, idempotent confirm.
- **Skill `test-driven-development`:** for each behavior: write failing test → run `php artisan test --filter=SupplyCalculations -- -v` (RED) → minimal code → rerun (GREEN) → refactor.

### T5.2 — Feature Tests (PRD §38)

| Owner | Files | Skills |
|---|---|---|
| `qa-testing-engineer` | `plugins/webkul/supplies/tests/Feature/*` | `test-driven-development` |

- `CreateSupplyTest`, `ConfirmSupplyTest`, `CancelSupplyTest`, `SupplierStatementTest`, `InventoryEffectTest`, `SaleFlowTest`, `ExpenseTest`.
- Use factories (`SupplyFactory` with states `draft`, `confirmed`, `cancelled`); `fake()` / `$this->faker` per `AGENTS.md`.
- **Skill `test-driven-development`:** one vertical tracer bullet at a time (test1→impl1, test2→impl2) — not horizontal slices (all tests then all impl).

### T5.3 — Integration Tests (Golden Paths)

| Owner | Files | Skills |
|---|---|---|
| `qa-testing-engineer` | `plugins/webkul/supplies/tests/Integration/*` | `test-driven-development` + `systematic-debugging` |

- **Golden Path A:** `Supply(draft→confirm) → Inventory(+stock) → Accounting(+payable) → Payment(-payable) → Supplier Statement(reconciles)` + `ProductQuantity` check + `JournalEntry` existence + idempotent double-confirm.
- **Golden Path B:** `Sale(draft→confirm) → Inventory(-stock, fails if insufficient) → Accounting(+receivable) → Receipt(-receivable) → Customer Statement`.
- Both must be **end-to-end** inside a single Pest test with real DB (not mocked).
- **Skill `systematic-debugging`:** when golden path fails, build tight loop `php artisan test --filter=GoldenPathA -v` → trace data flow `search_files("Operation")` → hypothesis → fix ONE variable.

### T5.4 — Reconciliation

| Owner | Files | Skills |
|---|---|---|
| `qa-testing-engineer` + `accounting-integration-engineer` + `inventory-integration-engineer` | `tests/Integration/ReconciliationTest.php` | `test-driven-development` + `systematic-debugging` |

- Seed known supplies/sales/payments; assert:
  - `sum(supplies.total) - sum(supplier_payments) == supplier_statement.balance` (via accounting).
  - `sum(ProductQuantity.quantity) == sum(supplies.net) - sum(sales.qty)` per product/warehouse.
- Any mismatch is a blocker.
- **Skill `systematic-debugging`:** Phase 1 evidence gathering — log data at each component boundary (supplies → Operation → JournalEntry → Statement) before proposing fix.

### T5.5 — Permissions Matrix

| Owner | Files | Skills |
|---|---|---|
| `qa-testing-engineer` | `tests/Feature/SupplyPolicyTest.php` | `laravel-expert` (Policies/Gates) + `test-driven-development` |

- For each role (Admin, Warehouse, Accountant, Manager): prove allowed vs forbidden actions (create/confirm/cancel/edit/delete/view financials/manage users) via `actingAs()` + policy assertions.
- Prove confirmed supply cannot be edited/deleted by any role (only cancelled via reversal).
- **Skill `requesting-code-review`:** security scan must flag any missing `SupplyPolicy` check as `logic_errors`.

### T5.6 — Cancellation / Reversal

| Owner | Files | Skills |
|---|---|---|
| `qa-testing-engineer` + `supply-domain-engineer` | `tests/Feature/CancelSupplyTest.php` | `test-driven-development` + `systematic-debugging` |

- Confirm → cancel → verify inventory return + accounting reversal exist, balances revert, audit trail (`confirmed_by`, `cancelled_by`, chatter) present.
- Draft delete allowed; confirmed hard-delete forbidden (attempt returns 403 / policy denial).

### T5.7 — Performance & Security Smoke

| Owner | Files | Skills |
|---|---|---|
| `qa-testing-engineer` + `backend-architect` | Manual + `tests/Performance/*` if needed | `simplify-code` (Efficiency + Quality reviewers), `requesting-code-review` (security scan), `codebase-inspection` |

- Lists paginated, filters server-side, no N+1 (assert query count or use `DB::enableQueryLog`).
- Searchable selects don't preload thousands.
- No `env()` outside config, no hidden-field auth, no floating-point money.
- **Skill `simplify-code`:** run Efficiency reviewer on hot paths (dashboard KPIs, large report queries) + Quality reviewer for redundant state.
- **Skill `requesting-code-review`:** static scan `grep "^+.*(api_key|secret|password|eval|subprocess.*shell)"` + baseline-aware `pest`/`pint`.
- **Skill `codebase-inspection`:** `pygount --format=summary plugins/webkul/supplies` before vs after cleanup to prove LOC/ratio improvement.

### T5.8 — Arabic / RTL QA

| Owner | Files | Skills |
|---|---|---|
| `localization-rtl-engineer` + `qa-testing-engineer` | Manual checklist | `dogfood` (visual QA) |

- Every form, table, report, validation message, PDF/ticket (if built) in Arabic and RTL-correct.
- **Skill `dogfood`:** systematic browser QA — navigate each role's views, `browser_console()` after every page, `browser_vision(annotate=true)` for RTL layout, collect screenshots in `dogfood-output/screenshots/`, generate `report.md` per template.

## Acceptance Criteria

- [ ] Unit tests for calculations + validation + transitions green (`test-driven-development` — every test failed first).
- [ ] Feature tests for create/confirm/cancel/statement/payment/inventory/sale/expense green.
- [ ] Both golden-path integration tests green.
- [ ] Reconciliation tests green (supply/accounting/inventory agree).
- [ ] Policy tests green for all four roles.
- [ ] Cancellation/reversal auditable and non-destructive.
- [ ] `dogfood` report: 0 Critical/High issues; `codebase-inspection` ratios healthy; `simplify-code` SAFE/CAREFUL applied.
- [ ] No high-severity Pint / security / N+1 / RTL issues.

## Exit Criteria (gate to production)

- `php artisan test --compact` fully green (or failures are documented `needs-human` with risk accepted).
- `vendor/bin/pint --dirty --format agent` clean.
- `npm run build` succeeds.
- Manual walkthrough: warehouse clerk creates → confirms → accountant pays → manager views statements — all in Arabic.
- **Skills gate:** `requesting-code-review` JSON `passed:true` + `simplify-code` summary committed + `dogfood` report committed + `codebase-inspection` final LOC recorded.

## Risks

- Accounting reconciliation may expose mapping gaps — fix mapping, not the test (`systematic-debugging` Phase 1).
- Stock reconciliation may fail if inventory moves not created atomically with supply confirm.

## Decisions Log

| Date | Decision | Owner | Rationale |
|---|---|---|---|
|  |  |  |  |

## Handoff Checklist → Phase 6 / Release

- [ ] QA report committed (`docs/cardboard-erp/qa-report.md`) + `dogfood-output/report.md`.
- [ ] `simplify-code` summary committed.
- [ ] `codebase-inspection` final metrics recorded.
- [ ] Known issues triaged (blocker vs deferred to Phase 6).
- [ ] `requesting-code-review` passed on `phase/5-qa` PR.
- [ ] `phase/5-qa` merged via `github-pr-workflow`; `main` is production-candidate.
- [ ] Tag `v1.0.0-rc` if green.

## Commands

```bash
# TDD cycle:
php artisan make:test --pest Unit/SupplyCalculationsTest --unit
php artisan make:test --pest Feature/ConfirmSupplyTest
php artisan make:test --pest Integration/GoldenPathATest
# RED:
php artisan test --compact --filter=test_net_weight_calculation -v   # must FAIL
# GREEN:
php artisan test --compact --filter=test_net_weight_calculation -v   # must PASS
php artisan test --compact --filter=Supply
php artisan test --compact
# Debugging when RED:
# skill_view(name='systematic-debugging') → trace search_files("Operation")
# Security + review:
# skill_view(name='requesting-code-review') → delegate_task reviewer
vendor/bin/pint --dirty --format agent
pygount --format=summary plugins/webkul/supplies   # codebase-inspection
# Dogfood:
# skill_view(name='dogfood') → browser_navigate + browser_console + browser_vision
```
