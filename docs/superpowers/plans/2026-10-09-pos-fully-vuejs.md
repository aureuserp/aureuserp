# POS Fully-Vue Terminal Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** After the Filament login, one Vue app mounted once at `/pos` owns every POS screen — register picker, opening control, till, orders, closing, cash — navigating client-side with no page reload and no duplicated business logic.

**Architecture:** A session-less Filament shell page mounts the existing Vue till app. Session resolution (list registers → open/resume → fetch boot) moves into Vue via thin HTTP controllers that wrap the existing `PointOfSale` facade. The Vue app's existing `state.screen` state machine is extended with `picker | opening | orders | closing`. Boot payload is fetched via `GET sessions/{session}/boot` (the `data-boot` injection becomes a fallback). Old Filament/Livewire surfaces stay live until each Vue replacement is tested, then are deleted (strangler).

**Tech Stack:** Laravel 11 + Filament 3 (PHP 8.2), Vue 3 (Options-free reactive store, no Pinia/Router), Vite, Pest (PHP tests), IndexedDB, Tailwind 4.

**Spec:** `docs/pos-fully-vuejs-plan.md`

## Global Constraints

- No comments in any shipped source (PHP, Blade, JS, Vue, CSS, tests) — conventions §13/§10. The sole allowed docblock is `@extends Factory<Model>`.
- No `git add`/`commit`/`push`/`checkout`/`stash`/`reset` by the agent — leave changes in the working tree — conventions §23. (The TDD "Commit" steps below are written for a human executor; the agent hands them over.)
- Never run `php artisan test`, `migrate*`, `db:seed`, `*:install`, or `npm run build` — hand the exact command to the user — conventions §24. `./vendor/bin/pint --dirty` is allowed.
- No business/accounting/inventory/permission logic reimplemented in JS or in new controllers — they delegate to the existing facade/services only.
- Every new/wired endpoint asserts `PosAccess` scoping exactly as the Filament/Livewire page it replaces (`reachesConfig`/`reachesSession`).
- New controllers follow the existing pattern: `extends Illuminate\Routing\Controller`, constructor-promoted service deps, `abort_unless(PosAccess::…, 403)` first, return `response()->json(['data' => …])`. Reference: `src/Http/Controllers/API/V1/ClosingControlController.php`, `SessionController.php`.
- Routes register in BOTH groups, mirroring existing dual registration: web `point-of-sale.till.*` (prefix `pos/till`, `['web', Authenticate::class, SubstituteBindings::class]`) in `routes/web.php`, and sanctum `admin.api.v1.point-of-sale.*` (prefix `admin/api/v1/point-of-sale`) in `routes/api.php`.
- Tests follow `tests/Feature/API/V1/OrderHistoryTest.php`: `require_once` the three helpers, `TestBootstrapHelper::ensurePluginInstalled('inventories'|'accounts'|'point-of-sale')`, `InventoryHelper::actingAsAdmin()`, `PosHelper::openSession(...)`, `$this->actingAs($user, 'sanctum')`, `route('admin.api.v1.point-of-sale.…')`. No `RefreshDatabase`.
- User-facing strings are `__('point-of-sale::…')` with the nested key path mirroring the class/view location — conventions §5/§7.
- New Vue screens are dark-mode aware: Tailwind theme tokens / `dark:` utilities only, no literal hex, no `bg-white`/`text-gray-900` alone — conventions §18.
- Pint is the PHP formatter; run `./vendor/bin/pint --dirty` before each handoff.

## Review Focus

- **Picker/opening while offline** — picker and opening require the server; offline the app must show a "connect to open a register" state and block `openRegister`/`confirmOpening`, never silently hang. Pinned in Task 10 (store offline guard) manual-smoke + Task 2 loading/error state.
- **Refresh-resume ambiguity** — on `/pos` reload with 0 or 2+ open sessions for the cashier, show the picker; with exactly 1, auto-resume. Must match `Registers::ownSession()`. Pinned in Task 1 (`GET registers` returns `own_session` hint) test + Task 9 store test-of-record.
- **Permission drift on `GET registers`** — a cashier must only see configs whose company they reach (`PosAccess::reachesConfig`); a config of another company must be absent. Pinned in Task 1 test `it('scopes registers to the user company')`.
- **Boot-via-API failure mid-open** — if `GET boot` fails after a session opens, the app must surface the error and leave the session resumable (not strand the cashier on a blank till). Pinned in Task 8 store error path + manual smoke.
- **Double-open / stale session** — opening a config that already has a live session must resume it, not create a second (existing `liveSessionFor ?? openSession` logic); the Vue picker must call the same path. Pinned in Task 1 `POST .../session` reuse test (already covered by `SessionController::open`→`openSession`; add `it('resumes an existing live session')`).

---

## File Structure

**New PHP**
- `src/Http/Controllers/API/V1/RegisterListController.php` — list the user's active configs with live-session + discardable flags, and discard a session. Thin; wraps the facade.
- `src/Http/Resources/V1/RegisterResource.php` — JSON shape for one register (config + live session summary).
- `src/Filament/Pos/Pages/Pos.php` — the single session-less `/pos` shell page (replaces `Home`/`Terminal` as the mount host).
- `tests/Feature/API/V1/RegisterListTest.php` — delegation + scoping + resume tests.

**New Vue** (`resources/js/till/components/`)
- `PickerScreen.vue`, `OpeningControlModal.vue`, `MoneyDetailsModal.vue`, `OrdersScreen.vue`, `ClosingControlModal.vue`, `CashMovementModal.vue`, `DraftOrdersModal.vue`, `TopBar.vue`.

**Modified**
- `routes/web.php`, `routes/api.php` — add `registers` + discard routes.
- `resources/views/filament/pos/pages/till.blade.php` — session-less mount, drop the `<livewire:…register-controls>` include and `data-boot`/`data-sync-endpoint` session coupling (keep `data-boot` optional).
- `app/Providers/Filament/PosPanelProvider.php` — set the `/pos` shell as the panel home; keep or retire the user-menu Livewire dispatches as flows migrate.
- `src/PointOfSalePlugin.php` — discover the new `Pos` page; stop discovering retired pages (last phase).
- `resources/js/till/main.js` — mount on `/pos`; resolve session via API when no `data-boot`.
- `resources/js/till/App.vue` — render new screens/modals.
- `resources/js/till/store.js` (a.k.a. `store/till.js`) — add session-resolution layer, `state.screen` values, screen actions.

**Deleted (final phase, after parity)**
- `src/Filament/Pos/Pages/{Registers,Home,Orders}.php`, `src/Filament/Pos/Pages/Terminal.php` (abstract base, once nothing extends it), `src/Livewire/RegisterControls.php`, `resources/views/filament/pos/pages/{registers,orders}.blade.php`, the modal partials, `resources/js/point-of-sale.js` (after fold-in).

---

## Phase 1 — Backend: register-list API (the only new endpoint)

### Task 1: `GET registers` + `POST registers/{config}/discard` controller

**Files:**
- Create: `plugins/webkul/point-of-sale/src/Http/Resources/V1/RegisterResource.php`
- Create: `plugins/webkul/point-of-sale/src/Http/Controllers/API/V1/RegisterListController.php`
- Modify: `plugins/webkul/point-of-sale/routes/api.php`
- Modify: `plugins/webkul/point-of-sale/routes/web.php`
- Test: `plugins/webkul/point-of-sale/tests/Feature/API/V1/RegisterListTest.php`

**Interfaces:**
- Consumes (existing): `PointOfSale::liveSessionFor(Config): ?Session`, `PointOfSale::isSessionDiscardable(Session): bool`, `PointOfSale::discardSession(Session): void`, `PosAccess::reachesConfig/reachesSession/companyIds`, `Registers::ownSession(): ?Session`, `SessionResource`.
- Produces: route names `point-of-sale.till.registers` (GET), `point-of-sale.till.registers.discard` (POST), and the sanctum twins `admin.api.v1.point-of-sale.registers` / `.registers.discard`. JSON: `{ data: RegisterResource[], own_session_id: int|null }` for index; `{ data: null }` for discard.

- [ ] **Step 1: Write the failing test**

Create `plugins/webkul/point-of-sale/tests/Feature/API/V1/RegisterListTest.php`:

```php
<?php

use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Models\Config;

require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    $this->user = InventoryHelper::actingAsAdmin();
    $this->warehouse = InventoryHelper::warehouse();
    $this->session = PosHelper::openSession($this->warehouse);
    $this->config = $this->session->config;

    $this->actingAs($this->user, 'sanctum');
});

it('lists active registers with live session and discardable flags', function () {
    $this->getJson(route('admin.api.v1.point-of-sale.registers'))
        ->assertOk()
        ->assertJsonPath('data.0.id', $this->config->id)
        ->assertJsonPath('data.0.live_session.id', $this->session->id)
        ->assertJsonStructure(['data' => [['id', 'name', 'is_active', 'live_session', 'is_discardable']], 'own_session_id']);
});

it('omits inactive registers', function () {
    $this->config->update(['is_active' => false]);

    $this->getJson(route('admin.api.v1.point-of-sale.registers'))
        ->assertOk()
        ->assertJsonCount(0, 'data');
});

it('scopes registers to the user company', function () {
    $other = Config::factory()->create(['company_id' => $this->config->company_id + 999, 'is_active' => true]);

    $ids = collect($this->getJson(route('admin.api.v1.point-of-sale.registers'))->json('data'))->pluck('id');

    expect($ids)->not->toContain($other->id);
});

it('reports the own single open session for auto-resume', function () {
    $this->getJson(route('admin.api.v1.point-of-sale.registers'))
        ->assertOk()
        ->assertJsonPath('own_session_id', $this->session->id);
});

it('discards a discardable session', function () {
    $this->postJson(route('admin.api.v1.point-of-sale.registers.discard', ['config' => $this->config->id]))
        ->assertOk();

    expect(PointOfSale::liveSessionFor($this->config->refresh()))->toBeNull();
});
```

- [ ] **Step 2: Run the test to verify it fails**

Hand to the user:
```bash
php artisan test --testsuite=PointOfSaleFeature --filter=RegisterListTest
```
Expected: FAIL — route `admin.api.v1.point-of-sale.registers` not defined.

- [ ] **Step 3: Create `RegisterResource`**

`plugins/webkul/point-of-sale/src/Http/Resources/V1/RegisterResource.php`:

```php
<?php

namespace Webkul\PointOfSale\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\PointOfSale\Facades\PointOfSale;

class RegisterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $live = PointOfSale::liveSessionFor($this->resource);

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'code'           => $this->code,
            'is_active'      => (bool) $this->is_active,
            'is_restaurant'  => (bool) $this->is_restaurant,
            'live_session'   => $live ? [
                'id'    => $live->id,
                'state' => $live->state,
                'name'  => $live->name,
                'user'  => $live->user?->name,
            ] : null,
            'is_discardable' => $live ? PointOfSale::isSessionDiscardable($live) : false,
        ];
    }
}
```

- [ ] **Step 4: Create `RegisterListController`**

`plugins/webkul/point-of-sale/src/Http/Controllers/API/V1/RegisterListController.php`:

```php
<?php

namespace Webkul\PointOfSale\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\PointOfSale\Facades\PointOfSale;
use Webkul\PointOfSale\Filament\Pos\Pages\Registers;
use Webkul\PointOfSale\Http\Resources\V1\RegisterResource;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Support\PosAccess;

class RegisterListController extends Controller
{
    public function index(): JsonResponse
    {
        $configs = Config::query()
            ->where('is_active', true)
            ->when(PosAccess::companyIds() !== [], fn ($query) => $query->whereIn('company_id', PosAccess::companyIds()))
            ->orderBy('sort')
            ->get();

        return response()->json([
            'data'           => RegisterResource::collection($configs),
            'own_session_id' => Registers::ownSession()?->getKey(),
        ]);
    }

    public function discard(Config $config): JsonResponse
    {
        abort_unless(PosAccess::reachesConfig($config), 403);

        $session = PointOfSale::liveSessionFor($config);

        abort_unless($session !== null, 404);
        abort_unless(PointOfSale::isSessionDiscardable($session), 422);

        PointOfSale::discardSession($session);

        return response()->json(['data' => null]);
    }
}
```

- [ ] **Step 5: Register routes**

In `plugins/webkul/point-of-sale/routes/api.php`, add the import after line 13 and the routes inside the sanctum group (after line 22):

```php
use Webkul\PointOfSale\Http\Controllers\API\V1\RegisterListController;
```
```php
Route::get('registers', [RegisterListController::class, 'index'])->name('registers');
Route::post('registers/{config}/discard', [RegisterListController::class, 'discard'])->name('registers.discard');
```

In `plugins/webkul/point-of-sale/routes/web.php`, add the import after line 16 and the routes inside the `point-of-sale.till.` group (after line 36):

```php
use Webkul\PointOfSale\Http\Controllers\API\V1\RegisterListController;
```
```php
Route::get('registers', [RegisterListController::class, 'index'])->name('registers');
Route::post('registers/{config}/discard', [RegisterListController::class, 'discard'])->name('registers.discard');
```

- [ ] **Step 6: Run the test to verify it passes**

Hand to the user:
```bash
./vendor/bin/pint --dirty
php artisan test --testsuite=PointOfSaleFeature --filter=RegisterListTest
```
Expected: PASS (5 tests). If `Config::factory()` needs company fields, mirror `PosHelper` config creation instead of a bare factory.

- [ ] **Step 7: Commit (human executor)**

```bash
git add plugins/webkul/point-of-sale/src/Http/Controllers/API/V1/RegisterListController.php \
        plugins/webkul/point-of-sale/src/Http/Resources/V1/RegisterResource.php \
        plugins/webkul/point-of-sale/routes/api.php plugins/webkul/point-of-sale/routes/web.php \
        plugins/webkul/point-of-sale/tests/Feature/API/V1/RegisterListTest.php
git commit -m "feat(pos): add register-list and discard API for the Vue picker"
```

---

## Phase 2 — The single `/pos` shell page + boot-via-API bootstrap

### Task 2: Session-less `Pos` shell page and blade

**Files:**
- Create: `plugins/webkul/point-of-sale/src/Filament/Pos/Pages/Pos.php`
- Modify: `plugins/webkul/point-of-sale/resources/views/filament/pos/pages/till.blade.php`
- Modify: `plugins/webkul/point-of-sale/src/PointOfSalePlugin.php` (discover `Pos`)
- Modify: `app/Providers/Filament/PosPanelProvider.php` (home URL)
- Test: `plugins/webkul/point-of-sale/tests/Feature/Filament/PosShellTest.php`

**Interfaces:**
- Produces: a page at slug `pos` (URL `/pos`) rendering the till blade with **no session binding**. Exposes `syncEndpoint()` (route `point-of-sale.till.orders.sync`) and `registersEndpoint()` (route `point-of-sale.till.registers`) as `data-*` attributes. No `bootPayload()` (boot is fetched by the client).

- [ ] **Step 1: Write the failing test**

`plugins/webkul/point-of-sale/tests/Feature/Filament/PosShellTest.php`:

```php
<?php

use Livewire\Livewire;
use Webkul\PointOfSale\Filament\Pos\Pages\Pos;

require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../../inventories/tests/Helpers/InventoryHelper.php';
require_once __DIR__.'/../../../Helpers/PosHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    TestBootstrapHelper::ensurePluginInstalled('point-of-sale');

    $this->user = InventoryHelper::actingAsAdmin();
    $this->actingAs($this->user);
});

it('mounts the pos shell without a session', function () {
    Livewire::test(Pos::class)->assertOk();
});
```

- [ ] **Step 2: Run to verify it fails**

```bash
php artisan test --testsuite=PointOfSaleFeature --filter=PosShellTest
```
Expected: FAIL — class `Pos` not found.

- [ ] **Step 3: Create the `Pos` page**

`plugins/webkul/point-of-sale/src/Filament/Pos/Pages/Pos.php`:

```php
<?php

namespace Webkul\PointOfSale\Filament\Pos\Pages;

use BackedEnum;
use Filament\Pages\Page;

class Pos extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $slug = 'pos';

    protected string $view = 'point-of-sale::filament.pos.pages.till';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return __('point-of-sale::filament/pos/pages/terminal.title');
    }

    public function syncEndpoint(): string
    {
        return route('point-of-sale.till.orders.sync');
    }

    public function registersEndpoint(): string
    {
        return route('point-of-sale.till.registers');
    }
}
```

(If `terminal.title` does not exist, add it to `resources/lang/en/filament/pos/pages/terminal.php`.)

- [ ] **Step 4: Rewrite the till blade (session-less mount)**

`plugins/webkul/point-of-sale/resources/views/filament/pos/pages/till.blade.php`:

```blade
<x-filament-panels::page>
    @vite('plugins/webkul/point-of-sale/resources/js/till/main.js')

    <div
        id="pos-till"
        wire:ignore
        data-sync-endpoint="{{ $this->syncEndpoint() }}"
        data-registers-endpoint="{{ $this->registersEndpoint() }}"
    ></div>
</x-filament-panels::page>
```

The `<livewire:…register-controls>` include and `data-boot`/`data-access-token` are gone; boot now comes from the API. (During the strangler window the old `Home`/`Terminal` pages keep their own blade copy if needed — this file is shared, so keep the old pages pointed at a temporary `till-legacy.blade.php` copy until they are retired in Phase 7, OR gate on presence of `data-boot` in `main.js`, which Task 8 does. Preferred: `main.js` handles both, so this single file serves both.)

- [ ] **Step 5: Discover the page + set home**

In `src/PointOfSalePlugin.php`, add `Pos::class` to the `pos`-panel page discovery branch (next to `Registers::class`/`Home::class`). In `app/Providers/Filament/PosPanelProvider.php`, point the panel home at the new page — e.g. `->homeUrl(fn () => \Webkul\PointOfSale\Filament\Pos\Pages\Pos::getUrl())` or set it as the default via `->pages([... Pos::class ...])` and remove `Registers` from navigation (keep the class until Phase 7). Verify the login redirect lands on `/pos`.

- [ ] **Step 6: Run to verify it passes**

```bash
./vendor/bin/pint --dirty
php artisan test --testsuite=PointOfSaleFeature --filter=PosShellTest
```
Expected: PASS.

- [ ] **Step 7: Commit**

```bash
git add plugins/webkul/point-of-sale/src/Filament/Pos/Pages/Pos.php \
        plugins/webkul/point-of-sale/resources/views/filament/pos/pages/till.blade.php \
        plugins/webkul/point-of-sale/src/PointOfSalePlugin.php \
        app/Providers/Filament/PosPanelProvider.php \
        plugins/webkul/point-of-sale/tests/Feature/Filament/PosShellTest.php
git commit -m "feat(pos): add session-less /pos shell page that hosts the Vue app"
```

---

## Phase 3 — Store: session-resolution layer (boot-via-API)

> Vue has no unit-test runner in this repo; these tasks are verified by the PHP endpoint tests (already green) plus the manual smoke script at the end. Keep each task to one focused store addition so a reviewer can gate it.

### Task 3: Add screen values + bootstrap entry to `main.js`

**Files:**
- Modify: `plugins/webkul/point-of-sale/resources/js/till/main.js`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/store.js` (constructor/`start`)

**Interfaces:**
- Consumes: `#pos-till` dataset `syncEndpoint`, `registersEndpoint`, optional `boot`.
- Produces: when `data-boot` is absent, `till.start()` initializes with no session and sets `state.screen = 'picker'`, then calls `till.loadRegisters()`. When `data-boot` is present (legacy pages), behaves exactly as today.

- [ ] **Step 1:** In `main.js`, read `element.dataset.registersEndpoint` alongside the existing `syncEndpoint`/`accessToken`. Pass a config object `{ bootData, syncEndpoint, registersEndpoint, accessToken }` into the till constructor instead of positional `boot`.

- [ ] **Step 2:** In the till constructor/`start()`: if `bootData` is present, keep the current path (`loadMaster(bootData)`, screen defaults to `floor`/`products`). If absent, set `this.registersEndpoint`, `this.state.screen = 'picker'`, and schedule `this.loadRegisters()` after mount.

- [ ] **Step 3:** Manual check (hand to user): `npm run build` then open `/pos` — the app mounts, no console errors, shows an empty picker shell (screens added next). Expected: no uncaught errors.

- [ ] **Step 4: Commit**

```bash
git add plugins/webkul/point-of-sale/resources/js/till/main.js plugins/webkul/point-of-sale/resources/js/till/store.js
git commit -m "feat(pos): bootstrap the Vue app session-less, default to picker screen"
```

### Task 4: `loadRegisters` / `openRegister` / `resumeRegister` / `discardRegister` store actions

**Files:**
- Modify: `plugins/webkul/point-of-sale/resources/js/till/store.js`

**Interfaces:**
- Consumes: `this.registersEndpoint`, the existing `requestJson(url, { method, body })` wrapper, endpoints derived from it (`configs/{id}/session`, `sessions/{id}/boot`). Base URL via `new URL(path, window.location.origin)`; the register endpoint gives the `pos/till` base.
- Produces on `this`:
  - `state.registers: []`, `state.registersLoading: bool`, `state.registersError: string|null`, `state.ownSessionId: int|null`.
  - `async loadRegisters()` → GET registers, fill `state.registers`, `state.ownSessionId`; if `ownSessionId` set and exactly that one, call `resumeRegister(ownSessionId)`.
  - `async openRegister(config)` → POST `configs/{config.id}/session` (returns `{ data: session }`), then `bootSession(session)`.
  - `async resumeRegister(sessionId)` → `bootSession({ id: sessionId })`.
  - `async discardRegister(config)` → POST `registers/{config.id}/discard`, then `loadRegisters()`.
  - `async bootSession(session)` → GET `sessions/{session.id}/boot`, `loadMaster(payload)`, persist `localStorage['pos-active-session'] = session.id`, set screen to `opening` if `session.state === 'opening_control'` else `resumeScreenFor`/`products`.

- [ ] **Step 1:** Add the `state` keys above to the reactive state block.

- [ ] **Step 2:** Implement `loadRegisters`, deriving the `pos/till` base from `this.registersEndpoint` and building the `configs/{id}/session` + `sessions/{id}/boot` URLs relative to it. Wrap in try/catch → `state.registersError`.

- [ ] **Step 3:** Implement `openRegister`, `resumeRegister`, `discardRegister`, `bootSession` per the Produces contract. `bootSession` must call the existing `loadMaster` with the boot payload shape (`payload.data ?? payload`).

- [ ] **Step 4:** Offline guard: at the top of `openRegister`/`discardRegister`/`bootSession`, if `this.syncQueue?.state.offline` (or `!navigator.onLine`), set `state.registersError` to the translated "connect to open a register" message and return. (Review Focus: picker/opening offline.)

- [ ] **Step 5:** Refresh-resume: on `loadRegisters`, if `localStorage['pos-active-session']` matches a register's `live_session.id`, prefer resuming it; else honor `own_session_id`; else stay on picker. (Review Focus: refresh-resume ambiguity — mirrors `ownSession`.)

- [ ] **Step 6:** Manual check (user): `npm run build`, open `/pos` with one open session → auto-resumes into till; with none → picker list renders (once Task 5 adds the component).

- [ ] **Step 7: Commit**

```bash
git add plugins/webkul/point-of-sale/resources/js/till/store.js
git commit -m "feat(pos): add session-resolution store actions (list/open/resume/discard/boot)"
```

---

## Phase 4 — Picker + opening screens (Vue)

### Task 5: `PickerScreen.vue`

**Files:**
- Create: `plugins/webkul/point-of-sale/resources/js/till/components/PickerScreen.vue`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/App.vue`

**Interfaces:**
- Consumes (inject `'till'`): `state.registers`, `state.registersLoading`, `state.registersError`, `openRegister(config)`, `resumeRegister(id)`, `discardRegister(config)`.
- Produces: rendered when `state.screen === 'picker'`.

- [ ] **Step 1:** Create `PickerScreen.vue`: full-screen grid of register cards (name, code, live-session badge). Each card: "Open"/"Resume" button → `openRegister(config)` or `resumeRegister(config.live_session.id)`; a trash/discard icon when `is_discardable` → `discardRegister(config)` with a confirm. Top-left live clock (reactive `setInterval`), bottom bar with Back-office link (`/admin`) and Logout (POST to the Filament logout route). All Tailwind + `dark:` tokens; strings via a `t(...)` helper or inline `__`-equivalent passed from boot (use existing i18n approach in the till). Show `registersError` as an `x-filament`-style callout when set.

- [ ] **Step 2:** In `App.vue`, import `PickerScreen` and render it with `v-if="state.screen === 'picker'"` at the top of the screen switch (before `floor`/`products`), bypassing the till chrome (no numpad/cart on picker).

- [ ] **Step 3:** Manual check (user): `npm run build`; `/pos` with no open session shows the register grid; clicking Open boots into opening/till; discard removes a discardable session.

- [ ] **Step 4: Commit**

```bash
git add plugins/webkul/point-of-sale/resources/js/till/components/PickerScreen.vue plugins/webkul/point-of-sale/resources/js/till/App.vue
git commit -m "feat(pos): register picker as a Vue screen"
```

### Task 6: `OpeningControlModal.vue` + `MoneyDetailsModal.vue`

**Files:**
- Create: `plugins/webkul/point-of-sale/resources/js/till/components/OpeningControlModal.vue`
- Create: `plugins/webkul/point-of-sale/resources/js/till/components/MoneyDetailsModal.vue`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/App.vue`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/store.js`

**Interfaces:**
- Consumes (existing endpoint): `POST sessions/{session}/opening-control` with `{ cash_balance_start, opening_notes }` (see `SessionController::confirmOpeningControl`). Bills for money-details come from `boot.bills` already loaded by `loadMaster`.
- Produces store additions: `state.openingCash`, `state.openingNotes`, `state.moneyDetailsOpen`, `state.moneyDetailsTarget` (`'opening'|'closing'`), `async confirmOpening()` → POST, update `state.session.state = 'opened'`, `state.screen = 'products'` (or `'floor'` if restaurant). `moneyDetailsTotal(rows)` computed from bill `value * count`.

- [ ] **Step 1:** Add the store state + `confirmOpening()` + `recordMoneyDetails(rows, target)` (sets `openingCash` or the closing buffer). Port the rounding helper behavior from `RegisterControls::roundAmount`/`moneyDetailsTotal` (lines 77-80, 140-149) — arithmetic only, no server call.

- [ ] **Step 2:** Create `OpeningControlModal.vue`: shown when `state.screen === 'opening'`. Cash input bound to `state.openingCash`, "Open money details" button → opens `MoneyDetailsModal`, notes textarea, Confirm → `confirmOpening()`. Dark-mode tokens; strings localized.

- [ ] **Step 3:** Create `MoneyDetailsModal.vue`: a table of `boot.bills` with a count per denomination, live total via `moneyDetailsTotal`, Confirm → writes the total into the opening (or closing) cash field and closes.

- [ ] **Step 4:** Wire both into `App.vue` (opening as a full screen, money-details as an overlay modal).

- [ ] **Step 5:** Manual check (user): open a register that needs opening control → opening screen appears → enter cash (or use money details) → Confirm → session becomes OPENED and the till loads. Confirm the server session row moved to `opened`.

- [ ] **Step 6: Commit**

```bash
git add plugins/webkul/point-of-sale/resources/js/till/components/OpeningControlModal.vue \
        plugins/webkul/point-of-sale/resources/js/till/components/MoneyDetailsModal.vue \
        plugins/webkul/point-of-sale/resources/js/till/App.vue \
        plugins/webkul/point-of-sale/resources/js/till/store.js
git commit -m "feat(pos): opening control and money details as Vue screens"
```

---

## Phase 5 — Orders screen (Vue)

### Task 7: `OrdersScreen.vue` + order actions (history/invoice/cancel/refund)

**Files:**
- Create: `plugins/webkul/point-of-sale/resources/js/till/components/OrdersScreen.vue`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/App.vue`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/store.js`

**Interfaces:**
- Consumes (existing endpoints, already wired via `boot.session`/`boot.config`): `GET sessions/{session}/orders?status&page&search` (`orders_endpoint`), `GET orders/{order}/refundable-lines` + `POST orders/{order}/refund` (`order_refundable_endpoint`/`order_refund_endpoint`, `__order__` placeholder), `POST orders/{order}/invoice` (`order_invoice_endpoint`), `POST orders/cancel` (`order_cancel_endpoint`), `GET orders/{order}/receipt` (`order_receipt_endpoint`). Response shape: `OrderHistoryResource` (id, uuid, name, state, amounts, lines, payments).
- Produces store additions: `state.orderHistory`, `state.orderHistoryLoading`, `state.orderHistoryPage`, `state.orderHistorySearch`, `state.orderHistoryStatus`, `state.selectedHistoryOrder`; `async loadOrderHistory({page,search,status})` (online), `historyOrdersOffline()` (local store + bounded recent from boot), `async invoiceOrder(order)`, `async cancelOrder(order)`, `async startRefund(order)` (loads refundable lines, reuses the existing refund flow).

- [ ] **Step 1:** Add store state + actions. `loadOrderHistory` calls the orders endpoint when online; when `offline`, populate `state.orderHistory` from the local records store plus `boot.orders` (recent) — no network. `startRefund` reuses the existing refund machinery already in the store (the current till already consumes `pending_refund`); do not duplicate refund math.

- [ ] **Step 2:** Create `OrdersScreen.vue`: left list (search box, status filter, pagination, offline banner when `offline`), right detail (lines, payments, totals). Action buttons: Invoice (`invoiceOrder`), Cancel (`cancelOrder`, draft-only per state), Refund (`startRefund` → existing refund screen), Print receipt (`order_receipt_endpoint`). Port `orders.blade.php`'s Alpine numpad to the existing Vue `Numpad`/`PaymentPad` component. Dark-mode tokens; localized strings.

- [ ] **Step 3:** In `App.vue`, render `OrdersScreen` with `v-if="state.screen === 'orders'"`; add a nav affordance (in `TopBar`, Task 9, or a temporary button) to set `state.screen = 'orders'` and back to `products`.

- [ ] **Step 4:** Manual check (user): online — search/paginate orders, invoice one, cancel a draft, refund one unit (confirm `POSRET` operation), print a receipt. Offline — the screen shows local + recent orders and refunds a locally-known order.

- [ ] **Step 5:** After parity confirmed, retire the Filament `Orders` page: remove it from `PointOfSalePlugin` discovery and delete `src/Filament/Pos/Pages/Orders.php` + `resources/views/filament/pos/pages/orders.blade.php` (do the delete in Phase 7 to keep this task reversible).

- [ ] **Step 6: Commit**

```bash
git add plugins/webkul/point-of-sale/resources/js/till/components/OrdersScreen.vue \
        plugins/webkul/point-of-sale/resources/js/till/App.vue \
        plugins/webkul/point-of-sale/resources/js/till/store.js
git commit -m "feat(pos): order history, invoice, cancel and refund as a Vue screen"
```

---

## Phase 6 — Closing + cash screens (Vue)

### Task 8: `ClosingControlModal.vue`, `CashMovementModal.vue`, `DraftOrdersModal.vue` + actions

**Files:**
- Create: `plugins/webkul/point-of-sale/resources/js/till/components/ClosingControlModal.vue`
- Create: `plugins/webkul/point-of-sale/resources/js/till/components/CashMovementModal.vue`
- Create: `plugins/webkul/point-of-sale/resources/js/till/components/DraftOrdersModal.vue`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/App.vue`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/store.js`

**Interfaces:**
- Consumes (existing endpoints via `boot.session`): `GET sessions/{session}/closing-control-report` (`closing_control_endpoint`), `POST sessions/{session}/close` (`close_endpoint`, body `{ cash_balance_end_real, closing_notes, balancing_account_id, payment_differences }`, see `SessionController::close` incl. `draft-orders`/`unbalanced` error codes), `POST sessions/{session}/cash-movements` (`cash_movement_endpoint`, `{ type:'in'|'out', amount, reason }`), `POST sessions/{session}/cancel-draft-orders` (`cancel_drafts_endpoint`), `GET sessions/{session}/sales-details` (`sales_details_endpoint`, streamed PDF).
- Produces store additions: `state.closingReport`, `state.closingLoading`, `state.closingError`; `async loadClosingControl()`, `async closeRegister(payload)` (handles `draft-orders` → open `DraftOrdersModal`, `unbalanced` → surface delta + balancing-account picker), `async recordCashMovement({type,amount,reason})`, `async cancelDraftOrders()`, `downloadSalesDetails()` (opens `sales_details_endpoint` in a new tab). The closing modal **renders** the server report only; it never recomputes expected amounts (Review Focus / spec risk).

- [ ] **Step 1:** Add the store state + actions. `closeRegister` must branch on the JSON `code` (`draft-orders`, `unbalanced`) exactly as `SessionController::close` returns them. On success set `state.sessionClosed = true` and return to picker (`loadRegisters`).

- [ ] **Step 2:** Create `ClosingControlModal.vue` (shown when `state.screen === 'closing'`): render expected vs counted per payment method from `state.closingReport` (copy-expected buttons like `RegisterControls::copyExpectedCash/Payment`), closing notes, money-details reuse (Task 6 modal with `target='closing'`), Close button → `closeRegister`. Show unbalanced delta + a balancing-account select when the server returns `unbalanced`.

- [ ] **Step 3:** Create `CashMovementModal.vue`: in/out toggle, amount, reason → `recordCashMovement`. Create `DraftOrdersModal.vue`: list the session's draft orders with "Cancel all & continue closing" → `cancelDraftOrders()` then retry `closeRegister`.

- [ ] **Step 4:** Wire into `App.vue`; add `loadClosingControl` + set `state.screen='closing'` trigger (from `TopBar`, Task 9).

- [ ] **Step 5:** Boot-via-API failure path (Review Focus): if `loadClosingControl`/`closeRegister` fail with a network error, set `state.closingError` and keep the session resumable; do not blank the till.

- [ ] **Step 6:** Manual check (user): open closing control (numbers come from the server), do a cash in and a cash out, attempt close with a draft present (draft modal appears), cancel drafts, close → balanced journal entry posts; verify in admin. Trigger an unbalanced close to see the delta + balancing-account path. Download sales-details PDF.

- [ ] **Step 7: Commit**

```bash
git add plugins/webkul/point-of-sale/resources/js/till/components/ClosingControlModal.vue \
        plugins/webkul/point-of-sale/resources/js/till/components/CashMovementModal.vue \
        plugins/webkul/point-of-sale/resources/js/till/components/DraftOrdersModal.vue \
        plugins/webkul/point-of-sale/resources/js/till/App.vue \
        plugins/webkul/point-of-sale/resources/js/till/store.js
git commit -m "feat(pos): closing control, cash movements and draft handling as Vue screens"
```

---

## Phase 7 — Top bar, PWA fold-in, and cleanup

### Task 9: `TopBar.vue` + PWA/print consolidation

**Files:**
- Create: `plugins/webkul/point-of-sale/resources/js/till/components/TopBar.vue`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/App.vue`
- Modify: `plugins/webkul/point-of-sale/resources/js/till/main.js`
- Modify: `app/Providers/Filament/PosPanelProvider.php`

**Interfaces:**
- Consumes: store screen setters, `recordCashMovement`, `loadClosingControl`, PWA install + receipt-print helpers currently in `resources/js/point-of-sale.js`.
- Produces: an in-app top bar with Orders / Cash In-Out / Close Register / Install App / Back office / Logout; replaces the Filament `userMenuItems` Livewire dispatches for the migrated flows.

- [ ] **Step 1:** Create `TopBar.vue` with the actions above, shown on every in-session screen (`v-if="state.screen !== 'picker'"`). Buttons set `state.screen` or open the relevant modal.
- [ ] **Step 2:** Fold `point-of-sale.js` (PWA install prompt, service-worker registration, receipt print) into the Vue bundle — import its functions from `main.js` or move them into a `till/pwa.js` module. Keep the `point-of-sale.pwa.*` routes.
- [ ] **Step 3:** In `PosPanelProvider.php`, remove the `userMenuItems` entries that dispatched to the retired closing/cash Livewire (`pos-close-register`, cash-in-out) now that the Vue `TopBar` owns them. Keep Back office + Logout.
- [ ] **Step 4:** Manual check (user): `npm run build`; every action reachable from the top bar; install prompt works; receipt prints.
- [ ] **Step 5: Commit**

```bash
git add plugins/webkul/point-of-sale/resources/js/till/components/TopBar.vue \
        plugins/webkul/point-of-sale/resources/js/till/App.vue \
        plugins/webkul/point-of-sale/resources/js/till/main.js \
        app/Providers/Filament/PosPanelProvider.php
git commit -m "feat(pos): in-app top bar and PWA/print fold-in"
```

### Task 10: Delete retired surfaces + dark-mode & offline hardening pass

**Files:**
- Delete: `src/Filament/Pos/Pages/{Registers,Home,Orders,Terminal}.php`, `src/Livewire/RegisterControls.php`, `resources/views/filament/pos/pages/{registers,orders}.blade.php`, the modal partials under `resources/views/filament/pos/partials/modals/`, `resources/js/point-of-sale.js`.
- Modify: `src/PointOfSalePlugin.php` (stop discovering deleted pages), `app/Providers/Filament/PosPanelProvider.php` (drop references), `plugins/webkul/point-of-sale/package.json` (drop the `esbuild` step for `point-of-sale.js`).

**Interfaces:** none produced; this removes dead code after parity.

- [ ] **Step 1:** Grep the repo for references to each class/view/asset before deleting: `grep -rn "RegisterControls\|Pages\\\\Registers\|Pages\\\\Home\|Pages\\\\Orders\|point-of-sale.js\|register-controls" plugins app`. Confirm only the migrated call sites remain.
- [ ] **Step 2:** Delete the files above. Update `PointOfSalePlugin` discovery and `PosPanelProvider`.
- [ ] **Step 3:** Dark-mode pass: open every new screen (picker, opening, orders, closing, modals, top bar) in light and dark; fix any non-token colour.
- [ ] **Step 4:** Offline hardening pass: verify reconnect flush, duplicate-uuid prevention, `already-settled`/`unbalanced`/`draft-orders` conflict handling, and mid-sale refresh recovery (cart restored from IndexedDB, session auto-resumed) across the new screens.
- [ ] **Step 5:** Run the full suite + build (user):
```bash
composer dump-autoload
php artisan point-of-sale:install --no-interaction
php artisan test --testsuite=PointOfSaleFeature
npm run build
```
Expected: green, including `TaxParityTest` (the client-total gate).
- [ ] **Step 6: Commit**

```bash
git add -A
git commit -m "chore(pos): retire Filament/Livewire POS surfaces superseded by the Vue app"
```

---

## Self-review notes

- **Spec coverage:** picker→Task 5; opening→Task 6; orders/invoice/cancel/refund→Task 7; closing/cash/drafts/sales-details→Task 8; boot-via-API→Tasks 3–4; `/pos` shell→Task 2; registers API gap→Task 1; top bar + PWA→Task 9; cleanup/dark-mode/offline→Task 10. All spec sections map to a task.
- **Placeholder scan:** PHP tasks carry full test + implementation code. Vue tasks carry exact state keys, action signatures, endpoint names, and screen-value wiring (full component bodies are left to the executor because the 4,500-line store's internal helpers must be read in context — each Vue task names the exact store members and existing components to reuse so nothing is invented).
- **Type/name consistency:** route names, endpoint keys (`orders_endpoint`, `closing_control_endpoint`, `order_refundable_endpoint`, etc.), controller methods (`confirmOpeningControl`, `close` with `draft-orders`/`unbalanced` codes), and `PosAccess` methods are taken verbatim from the read source.
- **Review Focus:** the five listed failure modes each have an owning task (offline picker/opening→Task 4 guard + Task 2/8 error states; refresh-resume→Task 1 `own_session_id` test + Task 4 logic; permission drift→Task 1 scoping test; boot-via-API failure→Task 8 error path; double-open→reuses existing `liveSessionFor ?? openSession`).
