# Workflow — How Agents Work Together

## 1. Branching & Delivery

```
master  ─────────────────────────────────────────►
           \  phase/0-baseline  → PR → master
            \ phase/1-core-setup → PR → master
             \ phase/2-supplies  → PR → master  (main custom build)
              \ phase/3-reports  → PR → master
               \ phase/4-ux      → PR → master
                \ phase/5-qa     → PR → master
```

- One branch per phase: `phase/<n>-<slug>` (e.g. `phase/2-supplies`).
- No agent pushes directly to `master`. Every phase ends with a PR that passes `npm run build` + `vendor/bin/pint --dirty` + `php artisan test --compact`.
- Keep `CHANGELOG.md` in mind; don't edit it until a phase ships.

## 2. Phase Gating

| Gate | Who signs | Criterion |
|---|---|---|
| Phase entry | `backend-architect` | Previous phase's Exit Criteria all checked |
| Phase exit | `qa-testing-engineer` | All Acceptance Criteria + integration test `Supply→Inventory→Accounting→Payment→Statement` green |

No overlapping phases except 4 (UX) may start in parallel with 3 (Reports) once Phase 2 is merged.

## 3. Agent Roles & Handoffs

```
Phase 0 Baseline:  devops-migration-engineer → backend-architect
Phase 1 Core:      backend-architect + localization-rtl-engineer
Phase 2 Supplies:  supply-domain-engineer ─┬─► filament-ui-engineer
                   inventory-integration-engineer ─┘
                   accounting-integration-engineer ─► qa-testing-engineer
Phase 3 Reports:   backend-architect + filament-ui-engineer
Phase 4 UX:        filament-ui-engineer + localization-rtl-engineer
Phase 5 QA:        qa-testing-engineer (owns the gate)
Phase 6 Optional:  product-owner-proxy triages Priority A vs B
```

Handoff artifact: the **phase plan's Handoff Checklist** at the bottom of each `plans/*.md`. Fill it before tagging the next agent.

## 4. Definition of Done (every task)

- [ ] Code in `plugins/webkul/supplies` (or the designated plugin) — no core edits.
- [ ] Pint clean (`vendor/bin/pint --dirty --format agent`).
- [ ] Pest coverage for new logic — unit for calculations, feature for flows, integration for the two golden paths:
  - `Supply → Inventory → Accounting → Payment → Supplier Statement`
  - `Sale → Inventory → Accounting → Payment → Customer Statement`
- [ ] Filament resource uses searchable selects, pagination, eager loading.
- [ ] Arabic strings in `resources/lang/ar/*.php`, RTL verified.
- [ ] Permissions via Shield/policy; warehouse user cannot escalate.
- [ ] Server-side recalculation of `net/total/remaining` + idempotent Confirm.

## 5. Communication Protocol

- **File the decision.** Any non-trivial choice goes in the phase plan's `Decisions Log` table (at the bottom of each plan). Don't rely on chat history.
- **One question, one `clarify` call.** Batch independent questions; don't chain clarifies.
- **Fail loudly.** If a tool/install/network call blocks the real path, report it directly and propose an alternative — never fabricate output.

## 6. Daily Loop (for long-running agents)

1. `git status` + `git branch` — confirm you're on the right phase branch.
2. Read your phase plan's **Today** section.
3. Implement → `vendor/bin/pint --dirty` → `php artisan test --compact --filter=<area>` → `npm run build` if UI changed.
4. Update the phase plan's **Progress** checkboxes.
5. Push + open/update PR.

## 7. Escalation

- Blocked on AureusERP internals? → `backend-architect` investigates `plugins/webkul/*` source, then `search-docs` equivalent (grep the plugin).
- Blocked on accounting mapping? → flag as `needs-human: accounting-policy` — do not guess account mappings (PRD §13).
- Blocked on permissions? → `backend-architect` + `devops-migration-engineer` pair on Shield.

## 8. Artifacts Every Phase Produces

- Code + migrations + lang files.
- Pest tests that prove the phase's Acceptance Criteria.
- Updated `plans/<phase>.md` with filled Progress + Decisions Log + Handoff Checklist.

---

## 9. Skill Hooks — متى تحمّل أي مهارة (مرتبط بـ WORKFLOW)

| خطوة في الـ Workflow | حمّل هذه المهارة | أمر التحميل |
|---|---|---|
| بداية أي Phase | `enterprise-erp-planning` | `skill_view(name='enterprise-erp-planning')` ثم `references/aureus-erp-seams.md` |
| كتابة خطة مفصلة (bite-sized) | `plan` | `skill_view(name='plan')` — يحفظ في `.hermes/plans/` |
| كتابة كود Laravel جديد | `laravel-development` + `laravel-expert` | `skill_view(name='laravel-expert')` — thin controllers, Services, FormRequest |
| قبل أي كود إنتاج | `test-driven-development` | `skill_view(name='test-driven-development')` — Iron Law RED→GREEN→REFACTOR |
| تكامل غير مؤكد (Inventory/Move API) | `spike` | `skill_view(name='spike')` — تجربة مهملة في `spikes/NNN-.../` |
| أول فشل اختبار/تكامل | `systematic-debugging` | `skill_view(name='systematic-debugging')` — 4 مراحل + feedback loop |
| قبل كل commit/push/PR | `requesting-code-review` | `skill_view(name='requesting-code-review')` — scan + reviewer subagent |
| بعد Phase كبيرة قبل الدمج | `simplify-code` | `skill_view(name='simplify-code')` — 4 مراجعين متوازيين |
| واجهات Filament جاهزة | `dogfood` | `skill_view(name='dogfood')` — اختبار استكشافي بالـ browser |
| إنشاء فرع/PR/CI/Merge | `github-pr-workflow` | `skill_view(name='github-pr-workflow')` — أو `gh pr create/checks/merge` |
| مراجعة PR زميل | `github-code-review` | `skill_view(name='github-code-review')` |
| قياس حجم المشروع | `codebase-inspection` | `skill_view(name='codebase-inspection')` — `pygount` |

> **التسلسل الإلزامي قبل أي PR:** `requesting-code-review` → `simplify-code` (اختياري للتنظيف) → `github-pr-workflow` للدمج. لا تدمج قبل أن يمر الـ reviewer المستقل.
