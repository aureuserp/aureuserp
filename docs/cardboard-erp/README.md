# Cardboard Trading ERP — Hermes Agent Workspace

> **Project:** Cardboard Trading ERP on **AureusERP** (Laravel 13 + Filament 5 + Livewire 4)  
> **Primary language:** Arabic (RTL) — English secondary  
> **Core rule:** **One Supply = One Supplier + One Material + One Weight Calculation + One Unit Price + One Warehouse**

---

## How This Workspace Is Organized

```
.agents/                  ← active agent instructions (gitignored, Hermes reads this)
  README.md               ← workspace map (this file)
  GUIDELINES.md           ← project-wide rules + AureusERP conventions
  WORKFLOW.md             ← collaboration, branching, Definition of Done
  INSTRUCTIONS.md         ← how to invoke agents, prompt templates
  plans/
    00-overview.md
    01-phase-0-baseline.md
    02-phase-1-core-setup.md
    03-phase-2-supply-module.md
    04-phase-3-reports.md
    05-phase-4-ux-simplification.md
    06-phase-5-qa.md
    07-phase-6-optional-features.md
  agents/
    backend-architect.md
    supply-domain-engineer.md
    filament-ui-engineer.md
    accounting-integration-engineer.md
    inventory-integration-engineer.md
    qa-testing-engineer.md
    localization-rtl-engineer.md
    devops-migration-engineer.md

docs/
  cardboard-erp/          ← human-readable mirror (committed)
```

> **Entry point for any agent:** read `GUIDELINES.md` → `WORKFLOW.md` → the phase plan you are assigned → your role file.

---

## Quick Start for Humans

1. **Pick a phase.** Start with `plans/01-phase-0-baseline.md` even if baseline looks obvious — it validates accounting/inventory config every later phase depends on.
2. **Spawn agents.** See `INSTRUCTIONS.md` § Spawning. Minimum team for Phase 2: `supply-domain-engineer` + `filament-ui-engineer` + `inventory-integration-engineer`.
3. **Follow Exit Criteria.** Nothing moves to next phase until every checkbox is green.

## Quick Start for Agents

```bash
cat .agents/GUIDELINES.md
cat .agents/WORKFLOW.md
cat .agents/plans/00-overview.md
cat .agents/agents/<your-role>.md
```

## V1 Scope (PRD §41)

| Area | In scope |
|---|---|
| Master data | Suppliers, Customers, Products (KG), Warehouses |
| Operations | Supplies (custom plugin), Sales (AureusERP), Supplier payments, Customer receipts, Expenses |
| Finance | Supplier/customer balances, Cash/bank, Basic accounting |
| Inventory | Stock + movements via AureusERP Inventory |
| Reports | Supplier/Customer statements, Supply/Sales/Inventory/Cash reports |
| System | Users, Roles, Audit, Arabic RTL |

**Out of V1:** manufacturing, HR, CRM, barcode, lot/batch, multi-material supply lines, AI/forecasting, mobile apps, portals, complex approvals.

## Golden Rules (full list in GUIDELINES.md)

1. Reuse before build — check `plugins/webkul/*` before creating any model/table.
2. One source of truth — inventory from `Webkul\Inventory`, balances from `Webkul\Accounting`.
3. Server-side calculations — net/total/remaining computed server-side; reject mismatched client values.
4. No hard deletes on confirmed records — cancel/reverse only.
5. Plugin, never core patch — custom code in `plugins/webkul/supplies`.

## Where AureusERP Docs Live

- Commit-pinned AureusERP version (Phase 0 decides the pin).
- Local plugin reference: `plugins/webkul/{inventories,purchases,sales,products,partners,accounting,security}/src`
- Filament patterns: sibling Resources under `plugins/webkul/*/src/Filament`
