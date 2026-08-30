# Guidelines — Cardboard Trading ERP on AureusERP

> Read this before writing any code. Violations block PRs.

---

## 1. AureusERP Is the Foundation — Adhere to Its Docs

### 1.1 Plugin-First, Never Core Patch

- Keep `aureuserp/aureuserp` core untouched. All business-specific code lives in **one dedicated plugin**: `plugins/webkul/supplies`.
- Follow the existing plugin contract exactly — see any `plugins/webkul/*/composer.json` and `*ServiceProvider.php`:
  ```php
  // plugins/webkul/supplies/composer.json
  {
    "name": "webkul/supplies",
    "extra": { "laravel": { "providers": ["Webkul\\Supplies\\SuppliesServiceProvider"] } },
    "autoload": { "psr-4": { "Webkul\\Supplies\\": "src/", "Webkul\\Supplies\\Database\\Factories\\": "database/factories/" } }
  }
  ```
- Register via `PackageServiceProvider` (`Webkul\PluginManager\PackageServiceProvider`), not a vanilla `ServiceProvider`. Mirror `InventoryServiceProvider`:
  ```php
  class SuppliesServiceProvider extends PackageServiceProvider {
    public static string $name = 'supplies';
    public static string $viewNamespace = 'supplies';
    public function configureCustomPackage(Package $package): void {
      $package->name(static::$name)->hasViews()->hasTranslations()->hasMigrations([...])->runsMigrations()->hasSettings([...])->hasDependencies(['invoices','inventories']);
    }
  }
  ```
- The `wikimedia/composer-merge-plugin` auto-discovers `plugins/*/*/composer.json` — no root `composer.json` edit needed beyond `composer dump-autoload` after scaffolding.

### 1.2 Reuse Before Build (PRD Principle 1)

Before creating any model/service/table, check:

| Need | Reuse |
|---|---|
| Suppliers / Customers | `Webkul\Partner\Models\Partner` (`partners_partners`, `account_type` enum) — do NOT create separate tables |
| Products / Materials | `Webkul\Product\Models\Product` (`products_products`) — add cardboard types as products with UOM = KG |
| Warehouses / Locations | `Webkul\Inventory\Models\Location`, `Webkul\Inventory\Models\Warehouse` |
| Inventory moves / stock | `Webkul\Inventory\Models\Move`, `MoveLine`, `Operation`, `ProductQuantity` + `InventoryManager`/`Inventory` facade |
| Sales | `Webkul\Sale\Models\Order` / `SaleManager` |
| Purchases / Bills | `Webkul\Purchase\Models\Order` |
| Accounting / Payments | `Webkul\Accounting` (`JournalEntry`, `JournalItem`), `Webkul\Account` |
| Users / Roles / Permissions | `bezhanSalleh/filament-shield` + `Webkul\Security` — reuse existing permission system |

If AureusERP already has a capability, **orchestrate it** from the Supplies domain — don't reimplement it.

### 1.3 One Source of Truth (PRD Principle 2)

- **Inventory quantities** come from `inventories_product_quantities` / `Inventory` services — never duplicate into `supplies` table.
- **Financial balances** come from `accounting` / `payments` — the `supplies.paid_amount` / `remaining_amount` columns are **denormalized convenience copies**; the authoritative balance is derived from accounting records (PRD §13).
- Reports must query AureusERP tables for inventory/finance; supplies table only for its own fields.

---

## 2. Architecture Constraints

### 2.1 Stack (AGENTS.md + composer.json)

- PHP 8.3, Laravel 13, Filament 5, Livewire 4, Pest 4, Pint, Tailwind 4.
- Follow `laravel-boost-guidelines` in `AGENTS.md` — constructor promotion, explicit return types, PHPDoc array shapes, curly braces always.

### 2.2 Supplies Plugin Structure (PRD §25.1)

```
plugins/webkul/supplies/
  composer.json
  src/
    Models/Supply.php
    Enums/SupplyStatus.php          # Draft, Confirmed, Cancelled
    Services/SupplyService.php
    Actions/{CreateSupply,ConfirmSupply,CancelSupply}.php
    Filament/Resources/SupplyResource.php
    Filament/Resources/SupplyResource/{Pages,Schemas,Tables}/
    Policies/SupplyPolicy.php
    Observers/SupplyObserver.php
    Providers/SuppliesServiceProvider.php
  database/migrations/
  database/factories/
  database/seeders/
  resources/lang/{ar,en}/
  tests/
```

### 2.3 Single-Supply Rule (PRD §3.1)

- One supply = exactly **one product**. No `supply_items` table in V1. If two materials arrive, they are two supplies. Enforce at validation + DB (no child lines table).

---

## 3. Data Integrity (PRD §27)

### 3.1 Server-Side Calculations — Non-Negotiable

```php
$net       = $gross - $tare;          // > 0, tare < gross
$total     = $net * $unitPrice;       // decimal(15,2) or (15,3) for weight
$remaining = $total - $paid;          // paid <= total, paid >= 0
```

- Never trust client-calculated `net_weight` / `total_amount` / `remaining_amount`.
- Server recalculates and **rejects** mismatched submissions.
- Use `decimal`/`numeric` columns, never `float`.

### 3.2 Transactions & Idempotency

- `ConfirmSupply` must wrap inventory + accounting + payment creation in a single DB transaction.
- Idempotency guard: `WHERE status = 'draft'` + unique confirmation token / optimistic lock so double-clicking Confirm cannot duplicate moves/journal entries.

### 3.3 Editing & Deletion (PRD §28–29)

| Status | Editable? | Deletable? |
|---|---|---|
| Draft | Yes | Yes (if business allows) |
| Confirmed | No direct edit of qty/price/supplier/warehouse/totals — correction via reversal only | Never hard-delete — Cancel/Reverse |
| Cancelled | Read-only | Never |

- `SupplyPolicy` enforces this; UI must also hide/disable controls (defense in depth, not UI-only).

---

## 4. Filament & UI Conventions

- Filament 5 Resource pattern: `src/Filament/Resources/SupplyResource.php` with `Schemas/SupplyForm.php`, `Tables/SuppliesTable.php`, `Pages/{List,Create,View,Edit}Supplies.php` — mirror `ProductResource` / `PartnerResource`.
- Use searchable relationship selects (`Select::make()->relationship()->searchable()->preload(false)`) — never load thousands of partners/products into a dropdown.
- Every list: pagination, server-side filtering, indexed FKs, eager-load relationships (`->with(['supplier','product','warehouse'])`) to avoid N+1.
- Navigation: keep it small. Hide unrelated AureusERP clusters from non-admin users via Shield permissions + `->navigationGroup()` placement (PRD §24).

---

## 5. Arabic / RTL (PRD §32)

- Primary locale `ar`, secondary `en`. Use `resources/lang/ar/*.php` + `spatie/laravel-translatable` where AureusERP does.
- All navigation, forms, validation messages, reports, printable tickets (Weighing Ticket) must be Arabic-first and RTL-correct.
- Currency display: EGP (`ج.م`). Use `ar-php` helper for number/date formatting where needed.
- Test RTL tables, filters, and PDF (dompdf) rendering on every UI PR.

---

## 6. Code Quality

- **Pint** before every commit: `vendor/bin/pint --dirty --format agent`
- **Pest** for all tests: `php artisan make:test --pest {Name}` then `php artisan test --compact --filter=...`
- Model factories + seeders for every new model.
- Validation via Form Requests (check sibling Form Requests for array vs string rule style).
- Config via `config/*.php` — never call `env()` outside config files.
- Queues: `ShouldQueue` for any integration that touches accounting/inventory and may be slow — but don't prematurely add Redis/queues (PRD §30) until a real need appears.

---

## 7. Security (PRD §31)

- Server-side permission checks on every action (policy + gate). Never rely on hidden UI fields.
- Warehouse users cannot touch accounting config or historical financials.
- Audit: reuse `HasLogActivity` / `HasChatter` traits where available; otherwise log `created_by / confirmed_by / cancelled_by + timestamps` on supplies.

---

## 8. Numbering (PRD §33)

- `SUP-2026-000001`, `SAL-2026-000001`, etc. Reuse `Webkul\Support\Services\SequenceService` if available (see `InventoryServiceProvider` / `PurchaseServiceProvider` references to it). Unique index on `reference`.

---

## 9. What Blocks a PR

- Client-side totals trusted without server recalculation.
- Duplicate inventory/accounting writes on double Confirm.
- Direct edit/delete of confirmed supply.
- New `suppliers` or `customers` table instead of `Partner`.
- New accounting ledger instead of orchestrating `Accounting`.
- Hard-coded English strings with no `__()` / lang file.
- N+1 in any table widget.
- Missing Pint / failing Pest for changed area.

---

## 10. Required Skills — Load Before You Code (إلزامي)

> كل وكيل يحمّل المهارات المذكورة قبل أي تنفيذ. `skill_view(name)` أولاً، ثم التطبيق.

| # | Skill | متى تحمّلها | المراحل | الآراء القوية التي تطبّقها |
|---|---|---|---|---|
| 1 | `enterprise-erp-planning` | بداية أي Phase + عند إنشاء plugin جديد | 0→6 كلها | Triple-sync (`.agents/.hermes/docs`), guideline triad, 7 مراحل, Dispatch Board. المصدر الذي بنى هذا التوثيق كله. |
| 2 | `laravel-development` | قبل أي كود PHP/Laravel | 1,2,3,4,6 | SOLID, Repository pattern, Eloquent بدل raw SQL, FormRequest, Service Container, strict types. |
| 3 | `laravel-expert` | قبل تصميم Services/Policies/API | 1,2,3,5 | Thin controllers, Services + FormRequest + Policies + Resources, eager loading, Sanctum, `declare(strict_types=1)`. |
| 4 | `test-driven-development` | قبل أي Feature أو إصلاح | 2,3,5 (وكل كود) | **Iron Law:** لا كود إنتاج قبل اختبار فاشل. RED→GREEN→REFACTOR عمودي (tracer bullet)، لا horizontal slices. |
| 5 | `systematic-debugging` | عند أول فشل اختبار/تكامل | 2,5 (وأي bug) | 4 مراحل: Root Cause → Pattern → Hypothesis → Fix + feedback loop ضيق + Rule of Three (≥3 فشل = مشكلة معمارية). |
| 6 | `requesting-code-review` | قبل كل `git commit/push/PR` | 0→6 كلها | Scan أسرار/حقن + baseline-aware tests/lint + مراجع مستقل `delegate_task` (fail-closed JSON) + حلقة auto-fix مرتين. |
| 7 | `simplify-code` | بعد كل Phase كبيرة قبل الدمج | 2,4,5 | 4 مراجعين متوازيين: Reuse/Quality/Efficiency/Altitude + تصنيف SAFE/CAREFUL/RISKY + Chesterton's Fence. |
| 8 | `plan` | عند تخطيط ميزة جديدة داخل Phase | 0,6 | خطة bite-sized (2-5 دقائق للمهمة), مسارات دقيقة, كود جاهز للنسخ, أوامر + مخرجات متوقعة. |
| 9 | `spike` | قبل بناء تكامل غير مؤكد | 0,2 (T2.7/T2.8) | تجربة مهملة: decompose→research→build→verdict (VALIDATED/PARTIAL/INVALIDATED) + comparison spikes 002a/002b. |
| 10 | `dogfood` | بعد اكتمال واجهات Filament | 4,5 | اختبار استكشافي منهجي بالـ browser: Plan→Explore→Collect→Categorize→Report مع لقطات + console errors. |
| 11 | `github-pr-workflow` | عند إنشاء فرع/PR/CI/Merge | 0→6 كلها | دورة `phase/<n>-<slug>`: branch → commit (Conventional) → push → `gh pr create` → `gh pr checks --watch` → squash merge. |
| 12 | `github-code-review` | مراجعة PR لزميل | 2,3,5 | تعليقات سطرية عبر `gh` أو REST, فحص diff, اقتراح تحسينات Laravel/Filament. |
| 13 | `codebase-inspection` | قياس حجم المشروع وتعقيده | 0,5 | `pygount` LOC/languages/ratios قبل وبعد التنظيف. |

**قاعدة تحميل:** في بداية كل Phase شغّل `skill_view(name='enterprise-erp-planning')` + المهارة الخاصة بالمرحلة (مثلاً Phase 2 → `spike` + `laravel-expert` + `test-driven-development`), وقبل كل PR شغّل `requesting-code-review`.
