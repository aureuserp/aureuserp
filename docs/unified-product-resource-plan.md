# Unified ProductResource — Migration Plan

## Problem
`ProductResource` is defined in the products plugin and extended separately by account, invoice,
inventories, sales, purchases, manufacturing, accounting. Inheritance **branches**:

```
Product (base)
├─ Account → Invoice → Sale        (tax / accounting fields)
│         └ Purchase
└─ Inventory → Manufacturing       (tracking / logistics fields)
```

Single-inheritance PHP cannot merge two branches, so **no resource has all fields**. Each plugin
also registers its **own** ProductResource (own cluster, routes, nav) and injects fields by fragile
**positional array-index mutation** of the parent's component tree. Result: inventory fields never
appear on the sales product screen and vice-versa; users navigate between plugins to edit one product.

## Goal
Every plugin keeps its **own** ProductResource (so Products nav stays in each plugin's cluster), but
all of them render **one unified** form / table / infolist that includes **every installed plugin's**
fields, columns, and relations.

## Architecture: shared static contribution registry
- The base `ProductResource` (products plugin) holds the registry (static arrays) + the core schema
  built from named **slots**.
- PHP inherited statics are shared: a plugin calling `ProductResource::contributeForm(...)` at boot
  writes to the single storage every subclass reads via `static::`. One registration → visible on all
  product screens.
- Each plugin's ProductResource becomes a **thin shell**: keeps cluster / nav / slug / pages, drops
  all `form()/table()/infolist()` overrides (inherits unified). Field defs move into `contribute*()`
  calls in that plugin's ServiceProvider `boot()`, guarded implicitly by the plugin being installed.
- **No deletions, no URL repointing** — all existing `ProductResource::getUrl()/::class` refs stay valid.

## Registry API (trait on base ProductResource)
```php
trait HasProductContributions
{
    protected static array $formContrib = [];      // slot => [ [priority, Closure], ... ]
    protected static array $tableContrib = [];
    protected static array $infolistContrib = [];
    protected static array $eagerLoad = [];

    public static function contributeForm(string $slot, Closure $factory, int $priority = 0): void;
    public static function contributeTable(string $slot, Closure $factory, int $priority = 0): void;
    public static function contributeInfolist(string $slot, Closure $factory, int $priority = 0): void;
    public static function contributeEagerLoad(array $relations): void;

    protected static function slot(array $bag, string $slot, mixed ...$args): array; // priority-sorted, flattened
}
```

## Slot catalog (replaces today's index-mutations)
| Slot | Replaces | Used by |
|------|----------|---------|
| `left.general.after` | `array_splice($left,1,0,$policy)` | accounts (invoice_policy, account props) |
| `left.inventory` (replace-or-default) | `$left[2] = richInventorySection` | inventories |
| `left.append` | `$left[] = customFields` | inventories, custom fields |
| `right.pricing.fields` | merge taxes into pricing | accounts |
| `right.append` | — | future |
| `form.hidden` | `Hidden::make('uom_id')`, `sale_line_warn` | accounts |
| `table.columns` | append columns | inventories |
| `table.filters.reject` | reject `responsible` constraint | invoices/sales/purchases |
| `table.filters.append` / `table.actions` / `table.bulkActions` | — | various |
| `infolist.*` | mirror of form slots | inventories, accounts |

Base renders core sections, then folds each slot's contributions at the marked position.
`left.inventory` = "replace if any contribution exists, else default" (preserves base GOODS section
when inventories absent).

## Model unification
Resource standardizes on base `Product`. Plugins inject at boot:
```php
Product::contributeFillable([...]);
Product::contributeCasts([...]);
Product::resolveRelationUsing('routes', fn (Product $p) => $p->belongsToMany(Route::class, ...));
```
- `contributeFillable/Casts` merged in the model constructor (registry filled at boot).
- Runtime relations via Eloquent `resolveRelationUsing()` — covers form `Select->relationship()`
  (query + sync), dot-columns, eager loading, and relation-manager pages (`$ownerRecord->{rel}()`).

### Relationship handling
- **Default**: `resolveRelationUsing()` per relation, co-located with that plugin's field contribution,
  referencing the related class inside the closure (only runs when the plugin is loaded).
- **Caveat**: relations added this way are not real methods → `method_exists()` returns false. A few
  Filament internals (and `ParentResourceRegistration`) probe with `method_exists`. For those specific
  relations, fall back to a **real method via a plugin trait** on the model the page targets.
- **Eager loading**: `contributeEagerLoad()` slot so contributed columns don't N+1.
- **Name collisions**: plugin-prefixed names where ambiguous.

## Phasing (each phase independently shippable + testable)
0. Registry trait on base resource + attribute/relation trait on base `Product`. No behavior change.
1. Refactor base `form/table/infolist` into core-builders + slot folds. **Visual checkpoint** — base unchanged.
2. Migrate **inventories**: fields → boot contributions; model attrs/relations → contribute*; strip its
   resource schema overrides (keep cluster/nav/pages). Inventory fields now appear on every product screen.
3. Migrate **accounts + invoice** (middle layers under sales): form/table overrides → contributions; strip overrides.
4. Migrate **sales**: strip table override → `table.filters.reject` contribution; shell only.
5. Later: purchases, manufacturing, accounting.
6. Regression matrix across plugin combos.

## Decisions (locked)
- Keep per-plugin ProductResources as thin shells (nav per plugin). No deletion, no URL repointing.
- Resource standardizes on base `Product` model.
- Relation pages stay per-plugin on their own ProductResource.

## Implementation status (2026-06-05)
Done + verified (lint, boot, tinker):
- **Base** (products): `ProductResource` delegates to `Schemas/ProductForm`, `Schemas/ProductInfolist`,
  `Tables/ProductsTable`; registry at `ProductResource/Support/ProductSchemaRegistry`. Base `Product`
  uses `Models/Concerns/HasProductAttributes` (contributed fillable/casts).
- **Inventories**: rich inventory form/infolist section → `left.inventory` slot; fillable/casts +
  `routes`/`responsible` relations registered on base `Product` in `packageBooted`. Resource is now a
  thin shell (nav/pages/model only).
- **Account** (middle layer for sales): tax selects → `right.pricing.fields`, invoice-policy + account
  properties → `left.general.after`, `sale_line_warn` → `hidden`; fillable + `productTaxes`/
  `supplierTaxes`/`propertyAccountIncome`/`propertyAccountExpense` relations on base `Product`. Form
  override stripped.
- **Sales / Invoice**: inherit the unified form automatically; their `table()` overrides (drop the
  `responsible` filter) are intentionally per-resource and kept. **Purchases inherits the unified form
  for free** (extends Account).

Verified: `Sale\Product`, `Inventory\Product`, `Purchase\Product` all inherit the inventory + account
fillable/casts/relations → every product screen renders & saves both field sets.

### Sub-navigation + header actions (approach B — per-resource pages)
Filament binds relation pages to one resource, so sub-nav/pages can't be shared like static schema.
Chosen: each product resource registers the union of pages as its own thin subclasses, guarded by
`Package::isPluginInstalled`. Header actions unified via a registry `actions('header', ...)` slot that
the base View/Edit pages fold in.
- Registry gained `actions()` / `renderActions()`.
- Base `ViewProduct`/`EditProduct` prepend `ProductSchemaRegistry::renderActions('header', $this)`.
- Inventories registers `UpdateQuantityAction` to `header`; its own View/Edit header-action overrides
  removed (inventory `EditProduct::beforeSave` kept). `moveLines`/`moves`/`quantities` relations
  resolved on base `Product`.
- Sales `ProductResource` now exposes `ManageQuantities` + `ManageMoves` (thin subclasses of the
  inventory pages, `$resource` rebound) in `getPages()` + `getRecordSubNavigation()`, guarded by
  inventories install. Attributes/Variants already shared (base relations).
- Verified: header slot resolves `UpdateQuantityAction`; sales product routes include moves/quantities.
- Same pattern repeats for purchases (Vendors) / manufacturing (BoM) when wanted.

### Reusable contribution system (support package)
The registry was generalized so any resource (Product, Partner, …) can reuse it:
- `Webkul\Support\Filament\Contributions\SchemaRegistry` — generic store keyed by a `scope` string
  (form/infolist/table/actions slots + eager-loads), priority-sorted slot resolution.
- `Webkul\Support\Filament\Contributions\AbstractSchemaRegistry` — scope-bound facade; subclass it and
  implement `scope()` to get a terse typed API (`form()/infolist()/table()/actions()/render*()/has*Slot()`).
- `Webkul\Support\Models\Concerns\HasContributedAttributes` — reusable model trait (`contributeFillable`
  / `contributeCasts`, merged via `initialize*`); each using-class gets isolated storage.
- `ProductSchemaRegistry` is now just `extends AbstractSchemaRegistry { scope() => 'product' }` — all
  existing call sites unchanged. Base `Product` uses the support trait.

**To unify a new resource (e.g. PartnerResource):** create a `PartnerSchemaRegistry extends
AbstractSchemaRegistry` (scope `'partner'`), split its base resource into `Schemas/PartnerForm` etc.
that fold registry slots, use `HasContributedAttributes` on the base Partner model, and have each
plugin contribute in `packageBooted` + register relation-page subclasses per resource (approach B).

### Deferred / TODO
- **Custom fields** (`HasCustomFields` "Additional" section) was previously appended only on the
  inventory product form. The inventory resource no longer overrides the form, so that section is
  temporarily gone. Next: add a single `left.append` custom-fields contribution keyed to the base
  ProductResource (one shared section, not per-plugin).
- Migrate remaining plugins' resource-specific bits (manufacturing/accounting) if/when needed.
- Optional: convert sales/invoice `responsible`-filter rejects to a per-resource hook; clean the
  purchases no-op form override.

## PartnerResource unification (in progress)
Reuses the same support-package registry (`scope = 'partner'`).
- **Phase 1 (done):** base `Webkul\Partner\...\PartnerResource` split into
  `PartnerResource/Schemas/PartnerForm`, `Schemas/PartnerInfolist`, `Tables/PartnersTable`, with a
  `Support/PartnerSchemaRegistry extends AbstractSchemaRegistry`. Slots: form/infolist
  `general.after`, `sales.fields` (inside Sales fieldset), `salesPurchase.append` (inside the
  Sales/Purchase tab), `tabs.append` (whole tabs); table `columns/filters.append/filters.reject/
  actions/bulkActions/groups`; `actions header`. Base `Partner` model uses `HasContributedAttributes`.
- **Phase 2 (done):** accounts migrated — its invoicing/sales/fiscal tabs + fields moved to
  `accounts/.../PartnerResource/Schemas/AccountPartnerSchema` and registered in `AccountServiceProvider::
  contributePartnerSchema()` (install-guarded); account form/infolist overrides stripped. Account
  fillable + 7 `property*` relations contributed to base `Partner`. Verified: `Contact\Partner`
  (extends base, not accounts) now inherits the account fillable/relations → the invoicing & sales
  tabs render and save on the Contacts/Website partner screens (the main gap), and on every
  customer/vendor screen across invoices/accounting/sales/purchases.

**Remaining (Phase 3 — sub-nav/actions, approach B):** propagate relation tabs uniformly — Bank
Accounts (accounts-installed) onto Contacts/Website; Bills + Purchases (purchase-only, relations on
`Purchase\Partner`) onto the other partner screens if desired — via thin per-resource page subclasses
+ `Partner::resolveRelationUsing` for `accountMoves`/`orders`, exactly like the product Moves/Quantities/
Vendors propagation. Header actions are already shared (defined only on base partner pages).

## Test matrix
Create/edit/list a product with combos: products-only; +inventories; +sales; all installed.
Verify: all fields present & saved to `products_products`; relation pages visible only when their plugin
is on; deep links from Orders/Quotations (`openProduct`) resolve; shield permissions intact.
