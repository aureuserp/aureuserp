# Phase 6 — Optional High-Value Features (Post-V1)

> **Objective:** Only after V1 is stable and QA-gated. Build **Priority A** first; **Priority B** only if the client confirms value. Every feature must pass: “Does this materially improve daily operations, financial control, stock accuracy, or reporting?” (PRD §40).

> **Skills:** `plan` (plan لكل ميزة bite-sized) → `laravel-expert` + `laravel-development` → `test-driven-development` → `dogfood` (لـ Ticket/Print) → `requesting-code-review` + `github-pr-workflow`

## Preconditions

- Phases 0–5 merged; `main` is production-candidate; QA report accepted.

## Priority A — Build If Client Confirms

### A1 — Weighing Ticket (Printable Receipt) (PRD §23.A1)

| Owner | Files | Skills |
|---|---|---|
| `filament-ui-engineer` + `localization-rtl-engineer` | `resources/views/supplies/ticket.blade.php`, `src/Filament/Resources/SupplyResource/Pages/ViewSupply.php` (Print action) | `plan` (bite-sized) + `laravel-expert` + `dogfood` |

- Fields: supply number, supplier, vehicle, gross/tare/net, unit price, total, paid, remaining, date, warehouse, QR/barcode optional.
- PDF via `barryvdh/laravel-dompdf` (already required); Arabic + RTL + EGP.
- Action on ViewSupply: “Print Ticket”.
- **Skill `plan`:** قبل البناء اكتب خطة bite-sized في `.hermes/plans/<date>-weighing-ticket.md` (Tasks 2-5 دقائق: Blade layout → dompdf Arabic font → PrintAction → test).
- **Skill `dogfood` بعد:** افتح التذكرة في المتصفح, `browser_vision` للتحقق من الـ RTL + EGP + QR.

### A2 — Vehicle Information (PRD §23.A2)

| Owner | Files | Skills |
|---|---|---|
| `supply-domain-engineer` | `src/Models/Supply.php` (already has vehicle_number/driver_name/phone), optional `Vehicle` lookup | `plan` + `laravel-expert` |

- V1 already has `vehicle_number`, `driver_name`, `driver_phone` on supplies — this task is **enrichment**: searchable vehicle history, driver phone validation, report filter by vehicle.
- Only add a `vehicles` table if traceability demand justifies it; otherwise keep nullable fields.
- **Skill `plan`:** قارن خيارين (nullable fields vs `vehicles` table) في الخطة — التزم بـ YAGNI.

### A3 — Daily Closing (PRD §23.A3)

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` + `filament-ui-engineer` | `src/Filament/Pages/DailyClosing.php`, `src/Services/DailyClosingService.php` | `plan` + `laravel-expert` + `test-driven-development` |

- Summary for a date: total supplies, total KG, total purchases, supplier payments, sales, customer collections, expenses, net cash movement.
- Both a Filament Page and a printable view; Arabic.
- Derived from authoritative tables — no separate closing ledger until needed.
- **Skill `test-driven-development`:** `DailyClosingTotalsTest` (seed known day → assert aggregates).

### A4 — Audit Trail (PRD §23.A4)

| Owner | Files | Skills |
|---|---|---|
| `backend-architect` | `Supply` model traits, `HasLogActivity`/`HasChatter` integration | `laravel-expert` |

- Track: created_by, confirmed_by, edited_by, payment created_by, cancellation actions.
- Reuse AureusERP chatter/log where possible; ensure `confirmed_by/at`, `cancelled_by/at` already on supplies (Phase 2).

## Priority B — Defer Unless Requested

### B1 — Attachments (PRD §23.B1)

| Skills | Notes |
|---|---|
| `plan` + `laravel-expert` | Weighing slip, invoice, supplier doc, delivery doc. Use Filament `FileUpload` + `spatie/laravel-media-library` if already in stack; otherwise simple `Storage` disk + `SupplyAttachment` model. |

### B2 — Supplier Performance (PRD §23.B2)

| Skills | Notes |
|---|---|
| `laravel-expert` + `test-driven-development` | Report: total KG, total value, avg price, supply count per supplier. Aggregates over `supplies`. |

### B3 — Profit Estimation (PRD §23.B3)

| Skills | Notes |
|---|---|
| `plan` + `laravel-expert` | **Do not ship** `sales - purchases` as “profit”. Only after accounting + stock valuation are validated; needs `product-owner-proxy` + accountant sign-off. |

## Task Sequencing

1. A1 Ticket (low complexity, high business value) → first.
2. A2–A4 in parallel if capacity.
3. B1–B3 only after A is shipped and client explicitly requests.

## Acceptance Criteria (per feature)

- [ ] Feature behind permission (who can print/attach/view closing).
- [ ] Arabic + RTL + EGP correct (`dogfood` visual proof for A1).
- [ ] Pest coverage for new logic (`test-driven-development` — RED→GREEN).
- [ ] No duplication of balances/inventory (same source-of-truth rule).

## Exit Criteria

- Client confirms Priority A value; features merged behind feature flags or direct release.
- QA reconciliation still green after each optional feature.
- **Skills gate:** `plan` لكل ميزة committed, `requesting-code-review` passed, `dogfood` للطباعة.

## Risks

- Profit estimation is high-risk for misrepresentation — gate with accountant review.

## Decisions Log

| Date | Decision | Owner | Rationale |
|---|---|---|---|
|  |  |  |  |

## Handoff Checklist → Release

- [ ] Optional features demoed in Arabic (`dogfood` screenshots for ticket).
- [ ] `plan` لكل ميزة في `.hermes/plans/` committed.
- [ ] `phase/6-optional` merged via `github-pr-workflow`; tag `v1.1.0` if applicable.
