# Phase 2 — Supply Module (Core Custom Build)

> **Objective:** Build the **only custom domain** — Supplies — as `plugins/webkul/supplies`. One supply = one supplier + one material + one weight calc + one price + one warehouse. This phase is the heart of the project.

> **Skills:** `spike` (prove seams first) → `laravel-expert` + `laravel-development` → `test-driven-development` (Iron Law) → `systematic-debugging` (on failure) → `requesting-code-review` + `simplify-code` + `github-pr-workflow` (before merge)

## Preconditions

- Phases 0–1 merged; Main Warehouse, KG UOM, products, partners, accounts exist.
- Accounting mapping document exists (or `needs-human` flagged and stubbed).
- **Before coding:** run `skill_view(name='spike')` and prove Inventory + Accounting seams with throwaway experiments (T2.7/T2.8).

## Tasks

### T2.0 — Spike the Seams First (إلزامي قبل T2.6)

| Owner | Files | Skills |
|---|---|---|
| `inventory-integration-engineer` + `accounting-integration-engineer` | `spikes/001-inventory-receipt/`, `spikes/002-accounting-payable/` | `spike` |

- **Decompose 2 spikes** (Given/When/Then):
  | # | Spike | Validates | Risk |
  |---|---|---|---|
  | 001 | inventory-receipt | Given a Supply net_weight, when `Operation(Receipt)+Move+MoveLine` created and `actionDone`, then `ProductQuantity` increases by net_weight | High |
  | 002 | accounting-payable | Given a Supply total_amount, when `JournalEntry` (payable + inventory) created, then supplier balance increases | High |
- Build each in `spikes/<NNN>-<name>/README.md` + runnable script (CLI or Pest). Keep throwaway — hardcode everything.
- **Verdict required:** `VALIDATED | PARTIAL | INVALIDATED` with surprises + recommendation for real build. Do NOT start T2.6 until both are VALIDATED or PARTIAL with documented constraints.
- **Head-to-head if competing approaches** (e.g. direct `Move` vs `InventoryManager`): use comparison spikes `002a`/`002b`.

### T2.1 — Scaffold Plugin

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` | `plugins/webkul/supplies/composer.json`, `src/Providers/SuppliesServiceProvider.php` | `enterprise-erp-planning` §2, `laravel-expert` |

- Mirror `plugins/webkul/inventories/composer.json` + `InventoryServiceProvider`.
- `PackageServiceProvider` with `static string $name = 'supplies'`, `hasViews()`, `hasTranslations()`, `hasMigrations([...])`, `runsMigrations()`, `hasDependencies(['partners','products','inventories','invoices' or 'accounting'])`.
- Verify `composer dump-autoload` + `php artisan migrate` picks up the plugin.
- **Skill `laravel-expert`:** `PackageServiceProvider` not vanilla Provider, `hasDependencies` correct, `declare(strict_types=1)`.

### T2.2 — Supply Enum & Model

| Owner | Files | Skills |
|---|---|---|
| `supply-domain-engineer` | `src/Enums/SupplyStatus.php`, `src/Models/Supply.php`, `database/factories/SupplyFactory.php` | `laravel-expert` (Eloquent, enums, factories), `test-driven-development` |

```php
enum SupplyStatus: string { case Draft = 'draft'; case Confirmed = 'confirmed'; case Cancelled = 'cancelled'; }
```
- Model `Supply` (`supplies` table): fillable per PRD §26 + `GUIDELINES.md §2.2`; casts: `status => SupplyStatus`, `date => datetime`, decimals.
- Relations: `supplier(): BelongsTo Partner`, `product(): BelongsTo Product`, `warehouse(): BelongsTo Location|Warehouse`, `creator/confirmedBy/cancelledBy`.
- Traits: `HasFactory`, `HasLogActivity`/`HasChatter` if available, `SoftDeletes` optional (prefer status over delete).
- Scopes: `draft()`, `confirmed()`, `forSupplier()`, `forProduct()`, `betweenDates()`.
- **Skill `test-driven-development`:** write `SupplyStatusTransitionsTest` FIRST (RED: draft→confirmed→cancelled + illegal transitions), watch it fail, then implement enum + model (GREEN).

### T2.3 — Migration

| Owner | Files | Skills |
|---|---|---|
| `supply-domain-engineer` | `database/migrations/xxxx_create_supplies_table.php` | `laravel-expert` (migrations, indexes) |

- Columns per `GUIDELINES.md §2.2` + PRD §26; `decimal(12,3)` weight, `decimal(15,2)` money, `decimal(12,3)` or `15,2` for computed fields.
- FKs: `supplier_id → partners_partners`, `product_id → products_products`, `warehouse_id → inventories_locations` (or warehouses — match Aureus inventory FK).
- Indexes: `supplier_id`, `product_id`, `warehouse_id`, `date`, `status`, `reference`.
- `reference` unique + generated via `SequenceService` (`supplies.supply` sequence) with fallback.
- **Skill `laravel-expert`:** include all column attributes in `up()`, reversible `down()`, `declare(strict_types=1)`.

### T2.4 — Validation & Server-Side Calculations

| Owner | Files | Skills |
|---|---|---|
| `supply-domain-engineer` | `src/Http/Requests/SupplyRequest.php` or Filament form validation, `src/Services/SupplyService.php` | `laravel-expert` (FormRequest), `test-driven-development` |

- Rules:
  - `gross_weight > 0`, `tare_weight >= 0`, `tare_weight < gross_weight`, `net_weight = gross - tare > 0` (server-computed, reject if submitted net mismatches).
  - `unit_price >= 0`, `paid_amount >= 0`, `paid_amount <= total`, money precision validated.
  - `supplier_id`, `product_id`, `warehouse_id`, `date` required; single-product enforced structurally.
- `SupplyService::calculate(array $data): array` centralizes `net/total/remaining`; called by Form `afterStateHydrated`/`live` for preview but **recalculated server-side on save**.
- **Skill `test-driven-development`:** `SupplyCalculationsTest` (RED: `gross 5500 tare 1500 → net 4000`, `net 4000 * 8.5 → 34000`, `paid 20000 → remaining 14000` + edge cases 0/rounding/`paid==total`) → GREEN minimal service.

### T2.5 — State Machine & Actions

| Owner | Files | Skills |
|---|---|---|
| `supply-domain-engineer` | `src/Actions/{CreateSupply,ConfirmSupply,CancelSupply}.php`, `src/Services/SupplyService.php` | `laravel-expert` (Services, transactions, idempotency), `test-driven-development` |

- `CreateSupply`: validate → calculate → generate reference → `status=draft` → save.
- `ConfirmSupply`:
  ```php
  DB::transaction(function () {
    $supply = Supply::where('id',$id)->where('status','draft')->lockForUpdate()->firstOrFail();
    // idempotency: if already confirmed, return early (no duplicate writes)
    $supply->recalculate(); // server truth
    $operation = InventoryIntegration::createReceipt($supply); // Operation + Moves
    $journal   = AccountingIntegration::createPayable($supply); // JournalEntry
    if ($supply->paid_amount > 0) PaymentIntegration::createSupplierPayment($supply);
    $supply->update(['status'=>'confirmed','confirmed_by'=>auth()->id(),'confirmed_at'=>now(),'operation_id'=>$operation->id]);
  });
  ```
- `CancelSupply`: only if `confirmed`; creates reversal (inventory return + accounting reversal) — never hard delete; sets `cancelled_*`.
- Events: `SupplyConfirmed`, `SupplyCancelled` for audit/chatter.
- **Skill `test-driven-development`:** `SupplyStatusTransitionsTest` + `ConfirmSupplyIdempotencyTest` (RED: double Confirm → one Operation/JournalEntry).
- **Skill `systematic-debugging`:** when integration fails, build tight loop `php artisan test --filter=ConfirmSupply -v`, trace data flow via `search_files("Operation")`.

### T2.6 — Filament Resource

| Owner | Files | Skills |
|---|---|---|
| `filament-ui-engineer` | `src/Filament/Resources/SupplyResource.php`, `Schemas/SupplyForm.php`, `Tables/SuppliesTable.php`, `Pages/{List,Create,View,Edit}Supplies.php` | `laravel-expert` (Filament), `dogfood` (after) |

- Mirror `ProductResource` / `PartnerResource` structure (Schemas/Tables/Pages split).
- Form sections:
  1. Basic: `supplier_id` (searchable Partner select), `product_id` (searchable Product select, filtered to cardboard), `warehouse_id`, `date`, `vehicle_number`, `driver_name/phone`, `notes`.
  2. Weight: `gross_weight` (live), `tare_weight` (live), `net_weight` (disabled, live computed via `afterStateUpdated`).
  3. Financial: `unit_price` (live), `total_amount` (disabled, live), `paid_amount` (live, max = total), `remaining_amount` (disabled, live).
- Table: `reference`, `date`, `supplier.name`, `product.name`, `net_weight`, `unit_price`, `total_amount`, `paid_amount`, `remaining_amount`, `status` badge; filters: date range, supplier, product, warehouse, status; searchable `reference`.
- Pages: `ListSupplies` (with `CreateAction`), `CreateSupply`, `EditSupply` (blocks edit if `confirmed`), `ViewSupply` (infolist + Confirm/Cancel actions).
- Actions: `ConfirmAction` (requires confirmation modal, idempotent), `CancelAction` (requires reason, only confirmed).
- Eager load: `->modifyQueryUsing(fn($q) => $q->with(['supplier','product','warehouse']))`.
- **Skill `laravel-expert`:** `->searchable()->preload(false)`, pagination, `with()` to avoid N+1, thin Resource (logic in Services).
- **Skill `dogfood` after:** exploratory browser QA on the form (fill invalid weights, double-click Confirm, check console).

### T2.7 — Inventory Integration

| Owner | Files | Skills |
|---|---|---|
| `inventory-integration-engineer` | `src/Services/InventoryIntegration.php` | `spike` (already T2.0), `laravel-expert`, `systematic-debugging` |

- On Confirm: create `Operation` (type = Receipt) + `Move` + `MoveLine` linking `product_id`, `quantity = net_weight`, `location_dest = warehouse`.
- Study `InventoryManager`, `Operation`, `Move`, `MoveLine`, `ProductQuantity` in `plugins/webkul/inventories/src`.
- Respect `OperationState` / `MoveState` transitions; call `actionConfirm` / `actionDone` as Aureus expects (inspect existing purchase/sale listeners `ComputePurchaseOrderListener`).
- On Cancel: create return operation or call `OperationCanceled` flow.
- **If T2.0 spike was PARTIAL:** document constraints here (e.g. requires `OperationType` pre-seeded).

### T2.8 — Accounting Integration

| Owner | Files | Skills |
|---|---|---|
| `accounting-integration-engineer` | `src/Services/AccountingIntegration.php`, `src/Services/PaymentIntegration.php` | `spike` (T2.0), `laravel-expert` |

- On Confirm: create `JournalEntry` (supplier payable + inventory value) per accounting policy from Phase 1.
- On Payment: create payment record via `Webkul\Accounting` / `payments` plugin; decrease payable, decrease cash/bank.
- Do NOT build a custom ledger; orchestrate existing `JournalEntry`/`JournalItem`.
- Supplier balance displayed in statements comes from accounting, not `supplies.remaining_amount` alone.

### T2.9 — Policies & Permissions

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` | `src/Policies/SupplyPolicy.php`, `database/seeders/ShieldSeeder` | `laravel-expert` (Policies/Gates) |

- `viewAny`, `view`, `create`, `update` (only draft), `confirm` (draft→confirmed), `cancel` (confirmed→cancelled), `delete` (only draft, maybe only admin).
- Map to Shield permissions; warehouse employee can `create`+`confirm` if authorized, accountant can `cancel`/`payment`, manager read-only.
- **Skill `requesting-code-review`:** security scan must flag missing policy checks.

### T2.10 — Lang & Numbering

| Owner | Files | Skills |
|---|---|---|
| `localization-rtl-engineer` | `resources/lang/{ar,en}/supply.php`, `src/Support/Sequence.php` | `laravel-expert` |

- Arabic keys for every label, validation message (PRD §34), status badge.
- `SUP-2026-000001` via `SequenceService`; unique DB index.

## Acceptance Criteria (PRD §35 — all 18)

- [ ] Draft supply can be created via Filament.
- [ ] Exactly one product selectable; validation rejects missing/extra.
- [ ] Gross/tare produce correct net (server recalculated).
- [ ] Total = net × unit_price correct; paid ≤ total; remaining = total − paid.
- [ ] Cannot confirm without required fields (server validation).
- [ ] Confirm creates correct inventory receipt (stock + operation visible in Inventory).
- [ ] Confirm creates correct financial obligation (journal/payable visible in Accounting).
- [ ] Initial payment recorded correctly (if paid_amount > 0).
- [ ] Supplier statement reflects the transaction (Phase 3 may stub, but data exists).
- [ ] Warehouse stock reflects net quantity.
- [ ] Unique reference generated (sequence).
- [ ] Double Confirm cannot duplicate transactions (idempotency tested — `test-driven-development`).
- [ ] Confirmed supplies cannot be silently edited/deleted (policy + UI).
- [ ] Cancellation uses reversal, remains auditable.
- [ ] Permissions enforced server-side (Pest proves warehouse user cannot escalate).
- [ ] Arabic UI works (RTL, labels, validation messages).

## Exit Criteria (gate to Phase 3)

- All Acceptance Criteria green + Pest suite for `Supply` (unit + feature + integration) passing.
- `php artisan test --compact --filter=Supply` green; `vendor/bin/pint --dirty` clean; `npm run build` if UI touched.
- Golden Path A integration test green: `Supply→Inventory→Accounting→Payment→Supplier Statement` reconciles.
- **Skills gate:** `requesting-code-review` passed (`security_concerns:[]`, `logic_errors:[]`), `simplify-code` run (4 reviewers), `github-pr-workflow` PR merged with `gh pr checks --watch` green.

## Risks

- Inventory `Operation`/`Move` API differs from expectation — **mitigated by T2.0 `spike`** (must be VALIDATED before T2.6).
- Accounting mapping ambiguity — escalate `needs-human` rather than guessing accounts.
- Double-confirm race — must be covered by `lockForUpdate` + status guard.

## Decisions Log

| Date | Decision | Owner | Rationale |
|---|---|---|---|
|  |  |  |  |

## Handoff Checklist → Phase 3

- [ ] `spike` verdicts committed in `spikes/*/README.md` (VALIDATED).
- [ ] `Supply` model + migration + factory + seeder merged.
- [ ] `SupplyResource` usable by warehouse employee (Arabic).
- [ ] Inventory + accounting integrations demoed end-to-end (tight loop `php artisan test --filter=GoldenPathA -v` green).
- [ ] `simplify-code` cleanup applied (SAFE tier auto, CAREFUL verified).
- [ ] `phase/2-supplies` merged via `github-pr-workflow`; `phase/3-reports` branched.
