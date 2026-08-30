# Phase 0 — Baseline

> **Objective:** Verify the AureusERP installation, environment, and module configuration so every later phase builds on truth, not assumptions. No custom code yet.

> **Skills:** `enterprise-erp-planning` (framework) → `plan` (baseline.md structure bite-sized) → `codebase-inspection` (LOC/ratios) → `spike` (verification experiments) → `github-pr-workflow` (branch/PR/CI) → `requesting-code-review` (gate)

## Preconditions

- Repo cloned at `D:/Mohamed/ERP/aureuserp`, branch `master`, `.env.example` present.
- PHP 8.3, Composer, Node available.

## Tasks

### T0.1 — Pin & Record AureusERP Version

| Owner | Files | Skills |
|---|---|---|
| `devops-migration-engineer` | `composer.json`, `composer.lock`, `docs/cardboard-erp/baseline.md` | `enterprise-erp-planning` §1, `codebase-inspection` |

- Run `composer show aureuserp/aureuserp` or inspect `CHANGELOG.md` + `composer.json` version constraints.
- Decide and record the **pinned version/commit** for V1 (and whether to track `master` or a tag). PRD §39 Phase 0.
- Document PHP / Laravel / Filament versions actually installed vs `AGENTS.md` expectations (AGENTS.md says 11, composer.json currently says 13 + Filament 5 — note the drift).
- **Skill `codebase-inspection`:** run `pygount --format=summary plugins/webkul` to record LOC/languages baseline.

### T0.2 — Environment & Database Verification

| Owner | Files | Skills |
|---|---|---|
| `devops-migration-engineer` | `.env`, `php artisan migrate:status`, `php artisan db:show` | `enterprise-erp-planning`, `spike` (if DB fails) |

- Copy `.env.example` → `.env`, `php artisan key:generate`, configure DB (check `docker-compose.yml` / `docker/`).
- `php artisan migrate` on a fresh DB; record any failures. If migration fails, apply `systematic-debugging` (Phase 1) + `spike` throwaway repro.
- Verify `storage/` and `public/storage` linkage.

### T0.3 — Module Inventory

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` | `plugins/webkul/*/composer.json`, `bootstrap/providers.php` | `enterprise-erp-planning` `references/aureus-erp-seams.md`, `codebase-inspection` |

- List every installed `plugins/webkul/*` plugin and its `ServiceProvider` (see `plugins/webkul/inventories`, `purchases`, `sales`, `products`, `partners`, `accounting`, `security`).
- Confirm `composer-merge-plugin` discovery works (`composer dump-autoload` then check `vendor/composer/installed.json` includes `webkul/*`).
- Note which modules will be hidden in Phase 4 but must remain enabled (don't disable prematurely).
- **Skill `codebase-inspection`:** record plugin LOC breakdown in baseline report.

### T0.4 — Arabic / RTL Verification

| Owner | Files | Skills |
|---|---|---|
| `localization-rtl-engineer` | `config/app.php` (`locale`, `fallback_locale`), `resources/lang/`, `plugins/*/resources/lang/` | `enterprise-erp-planning` |

- Check `locale = ar`, `fallback = en`, Filament language switch (`bezhansalleh/filament-language-switch`) configured.
- Verify RTL renders (Filament panel direction, tables).
- Note gaps for Phase 4 (missing `ar` keys).

### T0.5 — Accounting & Inventory Configuration Check

| Owner | Files | Skills |
|---|---|---|
| `accounting-integration-engineer` + `inventory-integration-engineer` | `plugins/webkul/accounting/src/*`, `plugins/webkul/inventories/src/*` | `enterprise-erp-planning` seams table, `spike` (if seam unclear) |

- Confirm chart of accounts exists, at least one cash/bank journal, one payable/receivable account, one inventory valuation account.
- Confirm at least one warehouse + location + operation type (Receipt/Delivery) exists.
- Confirm payment methods / journals visible in Filament.
- **Skill `spike`:** if Operation/Move or JournalEntry API is unclear, create `spikes/001-inventory-seam/` + `spikes/002-accounting-seam/` throwaway experiments with verdict VALIDATED/PARTIAL/INVALIDATED before Phase 2.
- **Flag** `needs-human: accounting-policy` if account mapping for Supply→payable is ambiguous (PRD §13 — do not guess).

### T0.6 — Baseline Document

| Owner | Files | Skills |
|---|---|---|
| `devops-migration-engineer` | `docs/cardboard-erp/baseline.md` | `plan` (bite-sized structure) |

- Produce a one-page baseline report: version pin, env, DB, module list, RTL status, accounting/inventory readiness, open risks.
- **Skill `plan`:** structure report with Goal → Context → Findings → Risks → Next Steps (bite-sized sections).

## Acceptance Criteria

- [ ] Pinned AureusERP version/commit recorded and agreed.
- [ ] Fresh `php artisan migrate` succeeds; `php artisan test --compact` runs (even if zero tests, command doesn't error).
- [ ] `npm run build` succeeds (or failure is documented with fix plan).
- [ ] Module inventory list committed.
- [ ] RTL smoke test: Arabic panel loads without layout breakage.
- [ ] Accounting: at least one cash/bank account + payable account identified (or `needs-human` flagged).
- [ ] Inventory: at least one warehouse/location/operation type identified.
- [ ] `docs/cardboard-erp/baseline.md` exists.

## Exit Criteria (gate to Phase 1)

- All Acceptance Criteria checked.
- No `needs-human` blocker unacknowledged by product owner.
- **Skills gate:** `codebase-inspection` baseline recorded, `spike` verdicts (if run) documented, `requesting-code-review` passed on `phase/0-baseline` PR.

## Risks

- Version drift (AGENTS.md vs installed) — resolve pin before building.
- Accounting policy undefined — will block Phase 2 Confirm integration.

## Decisions Log

| Date | Decision | Owner | Rationale |
|---|---|---|---|
|  |  |  |  |

## Handoff Checklist → Phase 1

- [ ] Baseline report reviewed by `backend-architect`.
- [ ] `phase/0-baseline` branch merged to `master` via `github-pr-workflow` (`gh pr create` → `gh pr checks --watch` → squash merge).
- [ ] Branch `phase/1-core-setup` created from updated `master`.
- [ ] `requesting-code-review` passed (security scan + reviewer subagent JSON `passed:true`).

## Commands

```bash
composer show aureuserp/aureuserp
pygount --format=summary plugins/webkul   # codebase-inspection
php artisan migrate:status
php artisan migrate --force
php artisan test --compact
npm run build
vendor/bin/pint --dirty --format agent
# spike if needed:
mkdir -p spikes/001-inventory-seam && echo "# 001: inventory-seam" > spikes/001-inventory-seam/README.md
```
