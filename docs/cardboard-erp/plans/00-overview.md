# 00 — Overview & Architecture

> **Read first.** Every other plan assumes you have read this file.

## 1. What We're Building (PRD §2, §42)

A **simple cardboard trading management system** on AureusERP that answers four questions:

1. What came in?  → Supplies (custom)
2. What went out? → Sales (AureusERP)
3. Who do we owe? → Supplier payables
4. Who owes us?   → Customer receivables

Flow: `Supplier → Supply Receipt → Warehouse Stock → Payable/Payment → Customer Sale → Collection → Cash/Bank → Reports`

V1 is intentionally small. The only custom domain is **Supplies** (`One Supply = One Material`). Everything else orchestrates AureusERP.

## 2. V1 Scope (PRD §41)

| In | Out (deferred) |
|---|---|
| Suppliers, Customers, Products (KG), Warehouses | Manufacturing, HR, CRM |
| Supplies (custom plugin), Sales, Payments, Expenses | Barcode, Lot/batch, Multi-material lines |
| Supplier/customer balances, Cash/bank | AI, Forecasting, BI, Mobile apps |
| Stock + movements | Portals, Complex approvals |
| 6 reports + dashboard KPIs + audit + Arabic RTL |  |

## 3. Architecture — Plugin, Not Fork (PRD §25–26)

```
aureuserp/aureuserp  (core — do not touch)
  └─ plugins/webkul/
       ├─ partners      → Supplier/Customer master (Partner)
       ├─ products      → Cardboard materials (Product, UOM=KG)
       ├─ inventories   → Warehouses, Locations, Operations, Moves
       ├─ purchases     → Purchase Orders (if needed for bill flow)
       ├─ sales         → Sale Orders
       ├─ accounting    → JournalEntry/Item, chart of accounts
       ├─ payments      → (via accounting)
       ├─ security      → Users, Roles, Shield
       └─ supplies      ← NEW — the only custom plugin (this project)
            src/Models/Supply.php
            src/Enums/SupplyStatus.php
            src/Services/SupplyService.php
            src/Actions/{Create,Confirm,Cancel}Supply.php
            src/Filament/Resources/SupplyResource.php
            database/migrations/
            resources/lang/{ar,en}/
```

**Source-of-truth rule:** Inventory from `Webkul\Inventory`, finance from `Webkul\Accounting`. `supplies` stores only its own fields plus denormalized `total/paid/remaining` for display; balances are derived from accounting.

## 4. Data Model — Supplies (PRD §26)

```
supplies
  id, reference (unique, SUP-YYYY-NNNNNN), supplier_id→partners_partners, product_id→products_products,
  warehouse_id / location reference (Aureus location id),
  date, vehicle_number?, driver_name?, driver_phone?, notes?,
  gross_weight decimal(12,3), tare_weight decimal(12,3), net_weight decimal(12,3) STORED but SERVER-COMPUTED,
  unit_price decimal(12,2), total_amount decimal(15,2), paid_amount decimal(15,2), remaining_amount decimal(15,2),
  status enum(draft,confirmed,cancelled),
  purchase_order_id? nullable, operation_id? nullable (inventory operation), journal_entry_id? nullable,
  created_by, confirmed_by?, confirmed_at?, cancelled_by?, cancelled_at?, timestamps
  indexes: supplier_id, product_id, warehouse_id, date, reference, status
```

No `supply_items` table — single-product rule is structural.

## 5. Supply Lifecycle (PRD §11)

```
Draft ──confirm (transaction + idempotency)──► Confirmed ──cancel/reverse──► Cancelled
  │                                              │  (no hard delete)          (read-only)
  └─delete allowed                               └─no direct edit of qty/price/supplier/warehouse
```

Confirm triggers: inventory receipt + purchase/bill obligation + supplier payable + optional initial payment (all in one DB transaction).

## 6. Integration Seams (where to look in AureusERP)

| Seam | Plugin | Key classes |
|---|---|---|
| Partner lookup | `partners` | `Webkul\Partner\Models\Partner`, `AccountType` enum |
| Product + UOM | `products` + `support` | `Webkul\Product\Models\Product`, `Webkul\Support\Models\UOM` |
| Warehouses | `inventories` | `Warehouse`, `Location`, `OperationType` |
| Inventory | `inventories` | `InventoryManager`, `Operation`, `Move`, `MoveLine`, `ProductQuantity` |
| Accounting | `accounting` / `accounts` | `JournalEntry`, `JournalItem`, `Account` |
| Sequence | `support` | `Webkul\Support\Services\SequenceService` |
| Auth/Roles | `security` | Shield, `User`, `Policy` |
| Chatter/Audit | `chatter` | `HasChatter`, `HasLogActivity` |

## 7. Cross-Cutting Concerns

- **Numbering:** `SUP-2026-000001` via `SequenceService` if available; fallback to DB sequence with unique index.
- **Decimal precision:** weight `decimal(12,3)`, money `decimal(15,2)`.
- **Permissions:** `SupplyPolicy` (view/create/update/confirm/cancel/delete) mapped to Shield roles: Administrator, Warehouse Employee, Accountant, Manager (PRD §5).
- **Arabic RTL:** every UI string via `__()` + `resources/lang/ar`, EGP formatting, RTL tables, dompdf Arabic for tickets.

## 8. Phases (PRD §39)

| Phase | Focus | Key deliverable |
|---|---|---|
| 0 Baseline | Pin version, verify env/DB/modules/RTL/accounting/inventory/payments | Go/no-go checklist |
| 1 Core Setup | Company, currency (EGP), warehouses, accounts, users/roles, products | Seed data + Shield |
| 2 Supply Module | Model/migration/Resource/validation/calculations/state machine/integrations | `plugins/webkul/supplies` shippable |
| 3 Reports | Supplier/Customer statements, Supply/Sales/Inventory/Cash reports | 6 reports + widgets |
| 4 UX | Hide noise, Arabic navigation, form ergonomics | Warehouse-clerk usability |
| 5 QA | Integration tests, reconciliation, permissions, cancellation | Gate to production |
| 6 Optional | Weighing ticket, daily closing, attachments, vehicle tracking | Priority A→B |

Each phase plan (`01-…` through `07-…`) contains: Objective, Preconditions, Tasks (with file ownership), Acceptance Criteria, Exit Criteria, Risks, Decisions Log, Handoff Checklist.

## 9. The Two Golden Paths (must be green before any release)

```
Golden Path A:  Supply (Draft→Confirm) → Inventory (+stock) → Accounting (+payable) → Payment (-payable) → Supplier Statement (reconciles)
Golden Path B:  Sale   (Draft→Confirm) → Inventory (-stock) → Accounting (+receivable) → Receipt (-receivable) → Customer Statement (reconciles)
```

Pest feature + integration tests cover both end-to-end.

## 10. How to Use This File

- **Humans:** confirm scope + architecture here matches stakeholder expectation before Phase 0.
- **Agents:** after `GUIDELINES.md` + `WORKFLOW.md`, read the phase plan you were assigned. This file is background, not your task list.

---

## 11. Skills Map for This Overview (اقرأ قبل أي Phase)

> هذا الملف نفسه بُني بمهارة `enterprise-erp-planning`. كل Phase تحمّل مهاراتها الخاصة — انظر الجدول.

| Phase | Skills المطلوبة | الغرض |
|---|---|---|
| **كل المراحل** | `enterprise-erp-planning` | الإطار العام: triple-sync + guideline triad + 7 مراحل. حمّل `references/aureus-erp-seams.md` معها. |
| 0 Baseline | `plan` + `codebase-inspection` + `spike` | كتابة خطط bite-sized + قياس LOC + تجارب تحقق سريعة |
| 1 Core Setup | `laravel-expert` + `laravel-development` | إعداد Laravel/Filament بمعايير صحيحة |
| 2 Supply | `spike` → `laravel-expert` → `test-driven-development` → `systematic-debugging` | إثبات Inventory/Move API قبل بناء Resource |
| 3 Reports | `laravel-expert` → `test-driven-development` | استعلامات بلا N+1 + reconciliation |
| 4 UX | `laravel-expert` → `dogfood` | Filament + QA استكشافي للـ RTL |
| 5 QA | `test-driven-development` → `systematic-debugging` → `requesting-code-review` → `simplify-code` → `dogfood` | التغطية الكاملة قبل الإنتاج |
| 6 Optional | `plan` → `laravel-expert` | تخطيط كل ميزة اختيارية |

**قبل كل PR في أي Phase:** `requesting-code-review` → `github-pr-workflow` (ثم `simplify-code` اختياري).

انظر `SKILLS_MAP.md` للخريطة الكاملة بكل مهارة ورأيها القوي ومتى تُحمّل.
