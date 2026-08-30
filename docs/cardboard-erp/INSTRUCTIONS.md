# Instructions — How to Invoke Agents

## 1. Where Agents Live

```
.agents/agents/
  backend-architect.md
  supply-domain-engineer.md
  filament-ui-engineer.md
  accounting-integration-engineer.md
  inventory-integration-engineer.md
  qa-testing-engineer.md
  localization-rtl-engineer.md
  devops-migration-engineer.md
  product-owner-proxy.md
```

Each file is a **prompt you paste** when spawning that specialist. It contains role, responsibilities, inputs, outputs, constraints, and the exact files that agent owns.

## 2. Spawning (Hermes `delegate_task`)

**Single agent:**
```js
delegate_task({ tasks: [{ goal: "Implement Phase 2 per .agents/plans/03-phase-2-supply-module.md as supply-domain-engineer. Read .agents/GUIDELINES.md, .agents/WORKFLOW.md, .agents/agents/supply-domain-engineer.md first.", context: "Branch: phase/2-supplies. AureusERP at D:/Mohamed/ERP/aureuserp. Plugin: plugins/webkul/supplies." }] })
```

**Parallel team (Phase 2 example):**
```js
delegate_task({ tasks: [
  { goal: "As supply-domain-engineer, implement Supply model/migration/service/actions per .agents/plans/03-phase-2-supply-module.md § Tasks 1-4", context: "..." },
  { goal: "As filament-ui-engineer, build SupplyResource + form/table/pages per .agents/plans/03-phase-2-supply-module.md § Tasks 5-6", context: "..." },
  { goal: "As inventory-integration-engineer, wire Confirm→Inventory Operation/Move per .agents/plans/03-phase-2-supply-module.md § Task 7", context: "..." },
]})
```

> **Rule:** every spawned agent's first step is reading `GUIDELINES.md` + `WORKFLOW.md` + its phase plan + its role file. If it doesn't, steer it.

## 3. Prompt Template

```
You are <ROLE> for the Cardboard Trading ERP (AureusERP).

Context:
- Repo: D:/Mohamed/ERP/aureuserp
- Branch: phase/<n>-<slug>
- Phase plan: .agents/plans/0X-*.md  (your source of truth for tasks + acceptance criteria)
- Your role file: .agents/agents/<role>.md
- Global rules: .agents/GUIDELINES.md + .agents/WORKFLOW.md
- PRD: C:/Users/moham.DESKTOP-VH22AN6/AppData/Local/hermes/attachments/Cardboard_Trading_ERP_PRD-2.md

Task: <copy the specific Tasks + Acceptance Criteria from the phase plan>

Constraints:
- Plugin-only: plugins/webkul/supplies (or the plugin named in the plan)
- Reuse AureusERP before building — search plugins/webkul/* first
- Server-side calculations + idempotent Confirm
- Pint + Pest before done

Deliver:
- Code + migrations + lang files
- Tests proving your Acceptance Criteria
- Updated phase plan Progress + Decisions Log
```

## 4. When to Use Which Agent

| Need | Agent |
|---|---|
| Overall design, plugin scaffold, data model, sequencing | `backend-architect` |
| Supply model, service, actions, validation, state machine | `supply-domain-engineer` |
| Filament Resource, forms, tables, pages, navigation | `filament-ui-engineer` |
| Journal entries, payables, SequenceService, supplier balances | `accounting-integration-engineer` |
| Operations, Moves, ProductQuantity, InventoryManager | `inventory-integration-engineer` |
| Pest suites, integration tests, reconciliation, gates | `qa-testing-engineer` |
| Arabic strings, RTL, EGP formatting, dompdf | `localization-rtl-engineer` |
| Migrations, seeders, env, deploy, version pin | `devops-migration-engineer` |
| Scope triage, Priority A vs B, stakeholder questions | `product-owner-proxy` |

## 5. Steering Running Agents

```js
delegate_task({ action: "list" })                          // see ids + status
delegate_task({ action: "steer", subagent_id: "abc", message: "You missed GUIDELINES §3.2 — wrap Confirm in DB::transaction and add idempotency guard." })
delegate_task({ action: "stop", subagent_id: "abc" })      // end early, partial result still returns
```

## 6. Verifying Work

Agents must run before claiming done:

```bash
vendor/bin/pint --dirty --format agent
php artisan test --compact --filter=Supply   # or the relevant area
npm run build                                # if any Filament/Tailwind touched
```

Review the phase plan's **Exit Criteria** — every checkbox needs a test or manual proof.

## 7. What “Ready to Start” Means

After this planning pack lands, you can say to any agent:

> “Start Phase 2 as `supply-domain-engineer`.”

…and it has everything it needs: PRD, guidelines, workflow, phase tasks, acceptance criteria, role boundaries, and the exact files to touch. No extra briefing required.

## 8. Human Override

If an agent proposes a second payment system, a custom ledger, or a `suppliers` table — stop it. Those are explicitly forbidden (PRD §13–14, GUIDELINES §1.2). Direct it to orchestrate AureusERP's existing capability.

## 9. Mirrors

- `.agents/` is the live workspace (gitignored, Hermes-native).
- `docs/cardboard-erp/` is the committed mirror for PR review / onboarding. Keep them in sync when you ship a phase.

---

## 10. Skill Loading — متى وكيف (إلزامي قبل كل استدعاء)

### 10.1 القاعدة الذهبية

> لا تكتب كوداً قبل `skill_view`. كل مهارة لها آراء قوية (opinionated) — تجاهلها يعني إعادة العمل.

```js
// قبل أي Phase — حمّل المخطط الرئيسي
skill_view(name='enterprise-erp-planning')
skill_view(name='enterprise-erp-planning', file_path='references/aureus-erp-seams.md')

// قبل كتابة كود Laravel — حمّل الخبرة
skill_view(name='laravel-expert')
skill_view(name='laravel-development')

// قبل أي Feature — TDD
skill_view(name='test-driven-development')

// عند الشك في تكامل — Spike
skill_view(name='spike')

// عند فشل — Debugging
skill_view(name='systematic-debugging')

// قبل كل PR — Review + Workflow
skill_view(name='requesting-code-review')
skill_view(name='github-pr-workflow')

// بعد Phase كبيرة — تنظيف
skill_view(name='simplify-code')

// بعد واجهات Filament — QA استكشافي
skill_view(name='dogfood')
```

### 10.2 خريطة المهارات حسب المرحلة

| Phase | حمّل هذه المهارات بالترتيب | لماذا |
|---|---|---|
| **0 Baseline** | `enterprise-erp-planning` → `plan` → `codebase-inspection` → `spike` | تخطيط + قياس حجم المشروع + تجارب تحقق من Inventory/Accounting |
| **1 Core Setup** | `enterprise-erp-planning` → `laravel-expert` → `laravel-development` | إعداد Company/EGP/Warehouse/Shield بمعايير Laravel الصحيحة |
| **2 Supply Module** | `spike` → `laravel-expert` → `test-driven-development` → `systematic-debugging` (عند الفشل) | Spike لـ Operation/Move API قبل بناء Resource، ثم TDD لكل حساب، و Debugging عند فشل التكامل |
| **3 Reports** | `laravel-expert` → `test-driven-development` | استعلامات تقارير بلا N+1 + اختبارات مطابقة (reconciliation) |
| **4 UX** | `laravel-expert` → `dogfood` | تبسيط التنقل + اختبار استكشافي بالـ browser للـ RTL |
| **5 QA** | `test-driven-development` → `systematic-debugging` → `requesting-code-review` → `simplify-code` → `dogfood` → `codebase-inspection` | تغطية كاملة + إصلاح منهجي + مراجعة مستقلة + تنظيف + QA استكشافي + قياس نهائي |
| **6 Optional** | `plan` → `laravel-expert` → `test-driven-development` | تخطيط كل ميزة اختيارية bite-sized قبل بنائها |

### 10.3 خريطة المهارات حسب الدور

| Agent | المهارات الأساسية |
|---|---|
| `backend-architect` | `enterprise-erp-planning` + `laravel-expert` + `plan` + `github-pr-workflow` |
| `supply-domain-engineer` | `laravel-expert` + `test-driven-development` + `spike` + `systematic-debugging` |
| `filament-ui-engineer` | `laravel-expert` + `dogfood` + `simplify-code` |
| `accounting/inventory` | `laravel-expert` + `spike` + `test-driven-development` + `systematic-debugging` |
| `qa-testing-engineer` | `test-driven-development` + `systematic-debugging` + `requesting-code-review` + `dogfood` + `codebase-inspection` |
| `localization-rtl-engineer` | `dogfood` (للتحقق البصري) |
| `devops-migration-engineer` | `codebase-inspection` + `github-pr-workflow` |
| `product-owner-proxy` | `plan` + `enterprise-erp-planning` |

### 10.4 تضمين المهارات في `delegate_task`

```js
delegate_task({ tasks: [{
  goal: "As supply-domain-engineer, implement T2.2–T2.5. LOAD SKILLS FIRST: skill_view('laravel-expert'), skill_view('test-driven-development'), skill_view('spike') for T2.7 seam.",
  context: "Branch phase/2-supplies. Follow TDD Iron Law: failing test → minimal code → refactor. Spike Operation/Move in spikes/001-inventory-receipt/ before Resource."
}]})
```

> **تذكير:** كل ملف في `plans/*.md` يحتوي الآن قسم `> **Skills:**` يحدد المهارات المطلوبة لتلك المرحلة — اقرأه قبل البدء. وكل ملف في `agents/*.md` يحتوي قسم `Skills to Load`.

