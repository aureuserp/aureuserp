# Cardboard Trading ERP — Agent Orchestration Index

> **Status:** Planning complete. All phases + roles ready to spawn. This file is the dispatch board.

## Workspace Locations

| Path | Purpose | Committed? |
|---|---|---|
| `.agents/` | **Live Hermes workspace** — agents read this | No (gitignored) |
| `.hermes/` | Mirror for tools expecting `.hermes/` | No (gitignored) |
| `docs/cardboard-erp/` | **Committed mirror** — PR review + onboarding | Yes |

All three stay in sync. Edit `.agents/` first, then `cp -r .agents/* .hermes/` and `cp -r .agents/* docs/cardboard-erp/` after each phase ships.

## Contracts

- **PRD:** `C:/Users/moham.DESKTOP-VH22AN6/AppData/Local/hermes/attachments/Cardboard_Trading_ERP_PRD-2.md` (+ copy at `docs/cardboard-erp/PRD-reference.md` if you add it)
- **Guidelines:** `GUIDELINES.md` (AureusERP plugin rules, source-of-truth, calculations, RTL) — **§10 جدول 13 مهارة**
- **Workflow:** `WORKFLOW.md` (branching, gating, DoD) — **§9 Skill Hooks**
- **Instructions:** `INSTRUCTIONS.md` (spawn prompts) — **§10 خريطة المهارات حسب المرحلة/الدور**
- **Skills Map:** `SKILLS_MAP.md` — **الخريطة المركزية لكل مهارة ورأيها القوي ومتى تُحمّل** ⭐ جديد

## Phase Dispatch Board

| # | Phase | Plan | Branch | Team (minimum) | Status |
|---|---|---|---|---|---|
| 0 | Baseline | `plans/01-phase-0-baseline.md` | `phase/0-baseline` | `devops-migration-engineer` + `backend-architect` | `READY` |
| 1 | Core Setup | `plans/02-phase-1-core-setup.md` | `phase/1-core-setup` | `backend-architect` + `localization-rtl-engineer` + `devops` | `READY` |
| 2 | **Supply Module** | `plans/03-phase-2-supply-module.md` | `phase/2-supplies` | `supply-domain` + `filament-ui` + `inventory` + `accounting` + `qa` | `READY` |
| 3 | Reports | `plans/04-phase-3-reports.md` | `phase/3-reports` | `backend-architect` + `filament-ui` + `accounting` + `inventory` | `READY` |
| 4 | UX Simplification | `plans/05-phase-4-ux-simplification.md` | `phase/4-ux` | `filament-ui` + `localization-rtl` | `READY` |
| 5 | QA & Hardening | `plans/06-phase-5-qa.md` | `phase/5-qa` | `qa-testing-engineer` (gate) | `READY` |
| 6 | Optional (A→B) | `plans/07-phase-6-optional-features.md` | `phase/6-optional` | `filament-ui` + `product-owner-proxy` | `READY` |

> `READY` = plan is complete and contains Tasks, Acceptance Criteria, Exit Criteria, Risks, Decisions Log, Handoff Checklist. No code yet — that is the agents' job.

## Agents

| Agent | File | Primary phases |
|---|---|---|
| `backend-architect` | `agents/backend-architect.md` | 0, 1, 2, 3, 4, 5 |
| `supply-domain-engineer` | `agents/supply-domain-engineer.md` | 2 |
| `filament-ui-engineer` | `agents/filament-ui-engineer.md` | 2, 3, 4, 6 |
| `accounting-integration-engineer` | `agents/accounting-integration-engineer.md` | 0, 1, 2, 3, 5 |
| `inventory-integration-engineer` | `agents/inventory-integration-engineer.md` | 0, 1, 2, 3, 5 |
| `qa-testing-engineer` | `agents/qa-testing-engineer.md` | 2, 3, 5 (gate) |
| `localization-rtl-engineer` | `agents/localization-rtl-engineer.md` | 1, 2, 4, 6 |
| `devops-migration-engineer` | `agents/devops-migration-engineer.md` | 0, 1, 5 |
| `product-owner-proxy` | `agents/product-owner-proxy.md` | 6 (and scope defense always) |

## How to Start Work (copy-paste)

**Start Phase 0:**
```
Read .agents/GUIDELINES.md §10, .agents/WORKFLOW.md §9, .agents/SKILLS_MAP.md, .agents/plans/01-phase-0-baseline.md, .agents/agents/devops-migration-engineer.md
Branch: phase/0-baseline
Skills to load: skill_view('enterprise-erp-planning'), skill_view('plan'), skill_view('codebase-inspection'), skill_view('spike')
Task: all Tasks in 01-phase-0-baseline.md; deliver docs/cardboard-erp/baseline.md
```

**Start Phase 2 (main build) — parallel team:**
```js
// كل وكيل يحمّل مهاراته أولاً — راجع SKILLS_MAP.md §2/§3
delegate_task({ tasks: [
  { goal: "As supply-domain-engineer, LOAD skill_view('spike'), skill_view('laravel-expert'), skill_view('test-driven-development') then implement T2.2–T2.5 per .agents/plans/03-phase-2-supply-module.md. Follow TDD Iron Law.", context: "Repo D:/Mohamed/ERP/aureuserp, branch phase/2-supplies, plugin plugins/webkul/supplies" },
  { goal: "As filament-ui-engineer, LOAD skill_view('laravel-expert'), skill_view('dogfood') then implement T2.6 per .agents/plans/03-phase-2-supply-module.md.", context: "Repo D:/Mohamed/ERP/aureuserp, branch phase/2-supplies" },
  { goal: "As inventory-integration-engineer, LOAD skill_view('spike') (T2.0 first) then implement T2.7 per same plan.", context: "Repo D:/Mohamed/ERP/aureuserp, branch phase/2-supplies" },
]})
```

**Start Phase 5 (gate):**
```
Read .agents/SKILLS_MAP.md §2 (Phase 5), .agents/plans/06-phase-5-qa.md + .agents/agents/qa-testing-engineer.md
Skills: test-driven-development → systematic-debugging → requesting-code-review → simplify-code → dogfood → codebase-inspection
Branch: phase/5-qa
Block merge if reconciliation or idempotency fails or requesting-code-review passed != true.
```

## What “Ready” Means

After this planning pack, any agent can be told **“Start Phase N as <role>”** and has:
- PRD + overview + phase tasks + acceptance/exit criteria + risks
- Global guidelines (plugin-only, source-of-truth, server-side calculations, RTL)
- Workflow (branching, gating, DoD)
- Role boundaries (who owns which files, who reviews what)
- Spawn prompt template + delegate_task examples

No extra briefing needed.

## Next Step for You

1. Review `docs/cardboard-erp/plans/00-overview.md` to confirm scope.
2. Say **“Start Phase 0”** and the agents begin. Or start at Phase 2 if baseline is already verified.
