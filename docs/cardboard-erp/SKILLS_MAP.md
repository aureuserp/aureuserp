# خريطة المهارات — Cardboard Trading ERP

> **الغرض:** دليل مركزي يربط كل مهارة شعبية قوية الرأي (opinionated) بموضعها الدقيق في التوثيق، المرحلة، والدور. حمّل المهارة بـ `skill_view(name='...')` قبل التنفيذ.

## كيف تستخدم هذا الملف

1. افتح المرحلة التي تعمل عليها (مثلاً `plans/03-phase-2-supply-module.md`).
2. اقرأ سطر `> **Skills:**` أعلى الملف — يحيلك هنا.
3. حمّل كل مهارة مذكورة عبر `skill_view` **قبل** كتابة أي كود.
4. بعد الانتهاء، شغّل `requesting-code-review` قبل أي PR.

---

## 1. المهارات المحمّلة — 13 مهارة أساسية

| # | Skill | الوصف المختصر | الآراء القوية (لماذا هي مهمة لمشروعنا) | المراحل | الأدوار | أين موثّقة |
|---|---|---|---|---|---|---|
| 1 | **`enterprise-erp-planning`** | تخطيط ERP على Laravel plugin stacks | Triple-sync (`.agents/.hermes/docs`), guideline triad, 7 مراحل, Dispatch Board. **هي التي بنت هذا التوثيق كله.** | 0→6 كلها | كل الأدوار (إلزامي أولاً) | `GUIDELINES §10`, `WORKFLOW §9`, `INSTRUCTIONS §10`, `plans/00-overview §11`, كل `plans/*.md` سطر Skills |
| 2 | **`laravel-development`** | إرشاد Laravel + SOLID | Repository pattern, Eloquent بدل raw SQL, Service Container, strict types, FormRequest. يمنع كود إجرائي/مكرر. | 1,2,3,4,6 | `backend-architect`, `supply-domain`, `accounting`, `inventory` | `GUIDELINES §2.1`, كل `plans/02-*,03-*,04-*` Tasks |
| 3 | **`laravel-expert`** | Senior Laravel (10/11+) إنتاجي | Thin controllers, Services + FormRequest + Policies + Resources + Gates + Sanctum, eager loading (لا N+1), `declare(strict_types=1)`, API versioning. | 1,2,3,5 | كل مهندسي الـ backend | `GUIDELINES §10`, `WORKFLOW §9`, `plans/02,03,04,05,06` |
| 4 | **`test-driven-development`** | TDD: RED→GREEN→REFACTOR | **Iron Law:** لا كود إنتاج قبل اختبار فاشل شاهدته يفشل. Vertical tracer bullets لا horizontal slices. يمنع اختبارات تمر فوراً (وهمية). | 2,3,5 (+ كل كود) | `supply-domain`, `qa-testing` (أساسي), كل مطور يكتب Feature | `GUIDELINES §6`, `WORKFLOW DoD`, `plans/03 T2.2-2.5`, `plans/04 T3.2-3.6`, `plans/06 كل Tasks` |
| 5 | **`systematic-debugging`** | تصحيح منهجي 4 مراحل | Root Cause → Pattern → Hypothesis → Fix + tight feedback loop + Rule of Three (≥3 محاولات = مشكلة معمارية). يمنع التخمين العشوائي. | 2,5 (وأي bug) | `supply-domain`, `qa-testing`, `accounting`, `inventory` | `plans/03 T2.5`, `plans/06 T5.3-5.7` |
| 6 | **`requesting-code-review`** | مراجعة قبل الدمج | Security scan (secrets/حقن) + baseline-aware tests/lint + مراجع مستقل `delegate_task` (fail-closed JSON `passed:true` فقط إذا القائمتان فارغتان) + حلقة auto-fix مرتين. | 0→6 كلها (قبل كل PR) | كل الأدوار (بوابة) | `WORKFLOW §8→9`, `INSTRUCTIONS §10`, كل `plans/*.md` Exit Criteria |
| 7 | **`simplify-code`** | تنظيف بـ 4 مراجعين متوازيين | Reuse / Quality / Efficiency / Altitude + تصنيف SAFE/CAREFUL/RISKY + Chesterton's Fence. أربعة أضيق وأعمق من مراجع واحد. | 2,4,5 | `filament-ui`, `qa-testing`, `backend-architect` | `WORKFLOW §9`, `plans/03 Exit`, `plans/05 T4.6`, `plans/06 T5.7` |
| 8 | **`plan`** | كتابة خطة bite-sized | كل مهمة 2-5 دقائق، مسارات دقيقة، كود جاهز للنسخ، أوامر + مخرجات متوقعة، DRY/YAGNI/TDD. | 0,6 | `backend-architect`, `product-owner-proxy` | `plans/01 T0.6`, `plans/07 كل Priority` |
| 9 | **`spike`** | تجربة مهملة قبل البناء | decompose→research→build→verdict `VALIDATED/PARTIAL/INVALIDATED` + comparison spikes `002a/002b`. يمنع بناء Resource قبل إثبات الـ API. | 0,2 (T2.0/2.7/2.8) | `inventory`, `accounting`, `supply-domain` | `plans/01 T0.5`, `plans/03 T2.0` (إلزامي), `plans/05 T4.1` |
| 10 | **`dogfood`** | QA استكشافي بالـ browser | Plan→Explore→Collect→Categorize→Report: `browser_navigate` + `browser_console()` بعد كل صفحة + `browser_vision(annotate)` + لقطات `MEDIA:` + تقرير `report.md`. | 4,5,6-A1 | `filament-ui`, `localization-rtl`, `qa-testing` | `plans/05 T4.4`, `plans/06 T5.8`, `plans/07 A1` |
| 11 | **`github-pr-workflow`** | دورة حياة PR | `phase/<n>-<slug>` → Conventional Commits → `gh pr create` → `gh pr checks --watch` → squash merge → `git push --delete`. | 0→6 كلها | `devops`, `backend-architect`, كل صاحب PR | `WORKFLOW §1`, كل `plans/*.md` Handoff |
| 12 | **`github-code-review`** | مراجعة PR زميل | تعليقات سطرية عبر `gh` أو REST على الـ diff، اقتراح تحسينات Laravel/Filament، inline comments. | 2,3,5 | `backend-architect` (مراجع) | `plans/03`, `plans/06` |
| 13 | **`codebase-inspection`** | قياس LOC/Languages | `pygount --format=summary plugins/webkul` — LOC, ratios, لغات قبل/بعد التنظيف. | 0,5 | `devops`, `qa-testing` | `plans/01 T0.1/0.3`, `plans/06 T5.7` |

---

## 2. خريطة الاستخدام حسب المرحلة (أين تُحمّل)

### Phase 0 — Baseline
```
skill_view('enterprise-erp-planning')          # الإطار + seams
skill_view('plan')                              # هيكلة baseline.md
skill_view('codebase-inspection')               # pygount baseline
skill_view('spike')                             # spikes/001-002 للتحقق
skill_view('github-pr-workflow')                # branch phase/0-baseline
skill_view('requesting-code-review')            # بوابة الخروج
```
موثّق في: `plans/01-phase-0-baseline.md` (سطر Skills + جدول T0.1/T0.3 + Exit Criteria)

### Phase 1 — Core Setup
```
skill_view('enterprise-erp-planning')
skill_view('laravel-expert') + skill_view('laravel-development')
skill_view('requesting-code-review')
skill_view('github-pr-workflow')
```
موثّق في: `plans/02-phase-1-core-setup.md`

### Phase 2 — Supply Module (الجوهر)
```
skill_view('spike')                              # T2.0 — إلزامي قبل T2.6
skill_view('laravel-expert') + ('laravel-development')
skill_view('test-driven-development')            # T2.2-2.5 Iron Law
skill_view('systematic-debugging')               # عند فشل integration
skill_view('requesting-code-review') + ('simplify-code') + ('github-pr-workflow')
skill_view('dogfood')                            # بعد T2.6 للتحقق البصري
```
موثّق في: `plans/03-phase-2-supply-module.md` (سطر Skills + T2.0/T2.2-2.6 + Exit)

### Phase 3 — Reports
```
skill_view('laravel-expert')                     # Query design بلا N+1
skill_view('test-driven-development')            # reconciliation tests
skill_view('systematic-debugging')               # عند divergence
skill_view('requesting-code-review')
```
موثّق في: `plans/04-phase-3-reports.md`

### Phase 4 — UX Simplification
```
skill_view('laravel-expert')
skill_view('spike')                              # T4.1 nav-hiding POC
skill_view('dogfood')                            # RTL + role screenshots
skill_view('simplify-code')                      # T4.6 Efficiency
skill_view('requesting-code-review')
```
موثّق في: `plans/05-phase-4-ux-simplification.md`

### Phase 5 — QA & Hardening (البوابة)
```
skill_view('test-driven-development')            # T5.1-5.6
skill_view('systematic-debugging')               # T5.3-5.4
skill_view('requesting-code-review')             # T5.5 + gate
skill_view('simplify-code')                      # T5.7
skill_view('dogfood')                            # T5.8 RTL
skill_view('codebase-inspection')                # T5.7 final LOC
skill_view('github-pr-workflow')                 # merge/Tag v1.0.0-rc
```
موثّق في: `plans/06-phase-5-qa.md` (سطر Skills + كل T + Skills gate)

### Phase 6 — Optional (A→B)
```
skill_view('plan')                               # لكل ميزة bite-sized
skill_view('laravel-expert')
skill_view('test-driven-development')
skill_view('dogfood')                            # A1 ticket
skill_view('requesting-code-review')
```
موثّق في: `plans/07-phase-6-optional-features.md`

---

## 3. خريطة حسب الدور (ماذا يحمّل كل وكيل)

| الوكيل | المهارات الإلزامية | ملف الدور |
|---|---|---|
| **backend-architect** | `enterprise-erp-planning` → `laravel-expert` → `plan` → `github-pr-workflow` + `requesting-code-review` + `codebase-inspection` | `agents/backend-architect.md` § Skills to Load |
| **supply-domain-engineer** | `spike` → `laravel-expert` → `test-driven-development` → `systematic-debugging` → `requesting-code-review` | `agents/supply-domain-engineer.md` |
| **filament-ui-engineer** | `laravel-expert` → `dogfood` → `simplify-code` → `requesting-code-review` | `agents/filament-ui-engineer.md` |
| **accounting-integration-engineer** | `spike` → `laravel-expert` → `test-driven-development` → `systematic-debugging` | `agents/accounting-integration-engineer.md` |
| **inventory-integration-engineer** | `spike` (أولاً) → `laravel-expert` → `test-driven-development` → `systematic-debugging` | `agents/inventory-integration-engineer.md` |
| **qa-testing-engineer** | `test-driven-development` → `systematic-debugging` → `requesting-code-review` → `simplify-code` → `dogfood` → `codebase-inspection` → `github-pr-workflow` | `agents/qa-testing-engineer.md` |
| **localization-rtl-engineer** | `dogfood` + `laravel-expert` | `agents/localization-rtl-engineer.md` |
| **devops-migration-engineer** | `codebase-inspection` → `github-pr-workflow` → `plan` | `agents/devops-migration-engineer.md` |
| **product-owner-proxy** | `plan` → `enterprise-erp-planning` | `agents/product-owner-proxy.md` |

كل ملف دور الآن يحتوي قسم `## Skills to Load` بجدول `Skill | متى | الأمر` — اقرأه قبل أي تنفيذ.

---

## 4. كيف تحمّل مهارة (الأمر الدقيق)

```js
// 1. التخطيط العام
skill_view(name='enterprise-erp-planning')
skill_view(name='enterprise-erp-planning', file_path='references/aureus-erp-seams.md')
skill_view(name='enterprise-erp-planning', file_path='references/erp-multi-phase-planning.md')

// 2. Laravel
skill_view(name='laravel-development')
skill_view(name='laravel-expert')

// 3. جودة / TDD / Debugging
skill_view(name='test-driven-development')
skill_view(name='systematic-debugging')
skill_view(name='requesting-code-review')
skill_view(name='simplify-code')

// 4. تخطيط / تجريب
skill_view(name='plan')
skill_view(name='spike')

// 5. QA استكشافي
skill_view(name='dogfood')

// 6. GitHub
skill_view(name='github-pr-workflow')
skill_view(name='github-code-review')
skill_view(name='github-issue-to-pr')   // عند تحويل Issue → PR

// 7. قياس
skill_view(name='codebase-inspection')
```

> **قاعدة:** لا تكتب كوداً قبل `skill_view`. كل مهارة opinionated — تجاهلها = إعادة عمل + رفض PR.

---

## 5. التسلسل الإلزامي قبل أي PR (مطبق في كل المراحل)

```
1. skill_view('requesting-code-review')     # scan + reviewer subagent
   └─ إن فشل → حلقة auto-fix (مرتين حد أقصى)
2. skill_view('simplify-code')               # اختياري: تنظيف 4 مراجعين
   └─ SAFE auto-apply → CAREFUL with tests → RISKY flag only
3. skill_view('github-pr-workflow')          # gh pr create → checks --watch → squash merge
```

موثّق في: `WORKFLOW.md §9` + كل `plans/*.md` Handoff Checklist (سطر `requesting-code-review` passed).

---

## 6. التحقق — هل كل شيء موثّق؟

| التحقق | الحالة |
|---|---|
| `GUIDELINES.md §10` يحتوي جدول 13 مهارة | ✅ |
| `WORKFLOW.md §9` جدول Skill Hooks | ✅ |
| `INSTRUCTIONS.md §10` تحميل حسب المرحلة + الدور + delegate_task | ✅ |
| `plans/00-overview §11` خريطة المراحل | ✅ |
| كل `plans/01-07` سطر `> **Skills:**` + أعمدة Skills في جداول Tasks | ✅ |
| كل `agents/*.md` قسم `## Skills to Load` | ✅ (9 ملفات) |
| هذا الملف `SKILLS_MAP.md` مركزي | ✅ |
| مزامنة `.agents` ↔ `.hermes` ↔ `docs/cardboard-erp` | يتم الآن |

---

## 7. مهارات إضافية (محمّلة عند الحاجة)

| Skill | متى تُستخدم | كيف |
|---|---|---|
| `github-issue-to-pr` | عند تحويل Issue إلى PR موثّق | `skill_view(name='github-issue-to-pr')` |
| `hermes-agent` | إعداد/توسيع Hermes نفسه | `skill_view(name='hermes-agent')` |
| `hermes-agent-skill-authoring` | كتابة SKILL.md جديدة للمشروع | `skill_view(name='hermes-agent-skill-authoring')` |
| `session-librarian` | تنظيم الجلسات بعد Phase كبيرة | `skill_view(name='session-librarian')` |

> هذه المهارات غير مطلوبة لكل Phase، لكنها متاحة عند الحاجة — حمّلها فقط إذا ظهرت الحالة.
