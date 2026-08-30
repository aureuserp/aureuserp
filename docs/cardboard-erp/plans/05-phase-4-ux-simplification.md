# Phase 4 — UX Simplification & Navigation

> **Objective:** Make the system feel like a **simple cardboard trading app**, not a giant ERP. Hide noise, deliver Arabic-first navigation, and optimize the Supply form for non-technical warehouse employees.

> **Skills:** `laravel-expert` (Filament) → `dogfood` (exploratory browser QA) → `spike` (nav hiding POC) → `simplify-code` (cleanup) → `requesting-code-review` + `github-pr-workflow`

## Preconditions

- Phases 2–3 merged; core flows + reports work.

## Tasks

### T4.1 — Navigation Restructure (PRD §24)

| Owner | Files | Skills |
|---|---|---|
| `filament-ui-engineer` | `src/Providers/SuppliesServiceProvider.php`, `plugins/webkul/*/src/*ServiceProvider.php` or Panel config | `laravel-expert` (Filament), `spike` (if hiding unclear) |

- Target nav (hide everything else from non-admins via Shield + panel config):
  ```
  Dashboard          → Overview (KPIs)
  Operations         → Supplies, Sales, Payments, Expenses
  People             → Suppliers, Customers (Partner filtered views)
  Inventory          → Products, Warehouses, Stock
  Finance            → Supplier Statements, Customer Statements, Cash Movement, Accounting
  Reports            → Supply Report, Sales Report, Inventory Report
  Settings           → Users, Roles, Company, Products, Payment Methods, Expense Categories
  ```
- Use Filament `navigationGroup()`, `navigationSort()`, `navigationIcon()`; cluster supplies under `Operations`.
- For AureusERP modules that can't be hidden via config, use Shield permissions to remove from non-admin panels rather than deleting routes.
- **Skill `spike`:** if hiding nav via Shield/panel config is uncertain, create `spikes/003-nav-hiding/` throwaway with verdict before touching production code.

### T4.2 — Supply Form Ergonomics

| Owner | Files | Skills |
|---|---|---|
| `filament-ui-engineer` | `src/Filament/Resources/SupplyResource/Schemas/SupplyForm.php` | `laravel-expert` |

- Single-screen form: supplier → material → weights → price → warehouse → confirm. No multi-step wizard unless user-tested.
- Live calculations with immediate feedback; `net_weight`/`total`/`remaining` disabled but visibly updating.
- Searchable selects with `preload(false)`; recent suppliers/products pinned or `getSearchResultsUsing` optimized.
- Validation messages in Arabic, inline, actionable (PRD §34).
- Mobile-friendly (warehouse tablets): large inputs, numeric keyboards (`->numeric()`, `->inputMode('decimal')`).

### T4.3 — Role-Based Visibility

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` | `src/Policies/*`, Shield config, `Filament/Clusters/*` | `laravel-expert` (Policies/Gates) |

- Warehouse Employee sees: Operations (Supplies, Sales if authorized), Inventory (view), no Finance config, no Settings/Users.
- Accountant sees: Operations (payments), Finance, Reports — no warehouse config.
- Manager sees: Dashboard + Reports + read-only Operations/Inventory.
- Enforce server-side; UI hiding is secondary.
- **Skill `requesting-code-review`:** must flag any missing policy check as `logic_errors`.

### T4.4 — Arabic Navigation & Labels

| Owner | Files | Skills |
|---|---|---|
| `localization-rtl-engineer` | `resources/lang/ar/*.php`, `plugins/webkul/*/resources/lang/ar/*.php` | `dogfood` (visual check) |

- Every nav item, form label, table header, filter, action, and empty state in Arabic.
- Verify RTL: tables, filters, pagination, modals, infolists all flow correctly.
- Date formatting: Arabic-friendly where appropriate; EGP suffix.
- **Skill `dogfood`:** after implementation, run exploratory browser pass (Phase: Explore → navigate each role's view, check RTL rendering, screenshot evidence).

### T4.5 — Empty States & Guidance

| Owner | Files | Skills |
|---|---|---|
| `filament-ui-engineer` | `src/Filament/Resources/*/Pages/*`, `resources/lang/ar/*.php` | `laravel-expert` |

- Helpful empty states: “No supplies yet — create your first supply” with CTA.
- Inline help: e.g. “Net = Gross − Tare, calculated automatically”.

### T4.6 — Performance Pass (PRD §30)

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` | All Resources, Widgets | `simplify-code` (Efficiency reviewer), `laravel-expert` |

- Verify: pagination everywhere, indexed FKs, searchable selects (no preload of thousands), eager loading, dashboard queries lightweight.
- No Redis/queue/Elasticsearch unless a measured bottleneck exists.
- **Skill `simplify-code`:** run 4-reviewer cleanup — Efficiency reviewer flags N+1/hot-path bloat, Reuse reviewer flags duplicated query helpers.

## Acceptance Criteria

- [ ] Non-admin users see only their permitted nav groups; unrelated AureusERP modules hidden.
- [ ] Supply form is single-screen, live-calculating, with searchable selects and Arabic validation.
- [ ] All nav labels, form labels, table headers in Arabic; RTL verified on tables/filters/modals (proven by `dogfood` screenshots).
- [ ] Role matrix proven: warehouse user cannot access accounting config (Pest + manual).
- [ ] Empty states and help text present in Arabic.
- [ ] No N+1, no unpaginated lists, no dropdown loading thousands.

## Exit Criteria (gate to Phase 5)

- Usability walkthrough with a non-technical persona (scripted) passes: “Add Supply” from login to confirmation in < 2 minutes.
- `vendor/bin/pint --dirty` clean; `npm run build` succeeds.
- **Skills gate:** `dogfood` report generated (`dogfood-output/report.md` with no Critical/High), `simplify-code` Efficiency passed, `requesting-code-review` passed.

## Risks

- Hiding AureusERP nav may require panel config or Shield overrides — **mitigated by `spike` 003**.

## Decisions Log

| Date | Decision | Owner | Rationale |
|---|---|---|---|
|  |  |  |  |

## Handoff Checklist → Phase 5

- [ ] `dogfood` evidence committed (screenshots per role + report).
- [ ] Nav + permissions demoed per role (screen recordings or live).
- [ ] `simplify-code` cleanup applied.
- [ ] `phase/4-ux` merged via `github-pr-workflow`; `phase/5-qa` branched.
