# Attendance Plugin

Neutral, source-agnostic daily attendance ledger for AureusERP. One row per
`(employee_id, work_date, source)` with naive-UTC `check_in` / `check_out`.
It owns no devices and no leave logic — it only records that someone
was present on a calendar day.

## What it is today

- Daily rows (`attendance_attendances`): `source='manual'` written through
  the Filament resource. No biometric writer exists yet.
- Writer keys: `source` is a stable machine key — `manual`, or
  `<writer>` / `<writer>:<id>` (e.g. `biometric-attendance:3`, `remote`) — and
  `source_label` is the human snapshot stored at write time. The writer part
  must be a slug registered in `WriterRegistry`; anything else is rejected on
  save, so no plugin can spoof another writer's key. Slugs are canonical
  lowercase (`[a-z0-9]` with `-`/`_` only inside, enforced fail-fast at
  registration). The ref part is always
  the writer's numeric record id — never free prose. Writers without records
  (a future `remote` plugin with no devices) simply use the bare slug.
  Any future writer plugin follows this shape with zero changes here:
  register its slug on boot (`WriterRegistry::register('my-plugin',
  fn (?int $id) => MyModel::find($id)?->name)`), then write rows server-side
  with `source = 'my-plugin'` or `'my-plugin:<id>'`. Display names resolve
  live from the writer when available and fall back to the stored snapshot,
  so rows stay readable even after the writer plugin is gone.
  Production wiring: the writer registers in its own service provider boot,
  validation happens at row-write time (after boot, so ordering is safe),
  and registration is idempotent per process (Octane-safe). Until a writer
  registers, only `manual` passes validation — that strictness is intended.
- Check-in and check-out are interpreted on the **employee's wall clock**
  (`employees.time_zone`, app timezone fallback) and stored as naive UTC.
  `work_date` is the calendar day in that same zone.
- Table with employee / source / date-range filters plus quick periods
  (today, this week, this month), a monthly stats widget, and Excel export.
  Export runs on the queue like every other Filament export: a queue worker
  (`php artisan queue:listen`, part of `composer run dev`) must be running,
  otherwise only the "started" toast appears and no file is ever produced.
- Reads `employees` (identity, `barcode`, `time_zone`, `company_id`).
  Calendar scheduled-hours and time-off leave overlays are planned as
  display-only reads; they are **not wired yet**.

## Rules the plugin enforces

- Check-in and check-out must both fall inside `work_date`. There is
  **no night-shift support in V1**: a checkout past midnight is rejected,
  not silently attached to the previous day.
- Open rows (no check-out) stay completable at any age; closed device rows
  are frozen; closed manual rows are editable only while recent, older ones
  are fixed via delete + recreate (visible in the activity log).
- `worked_minutes` is derived on read (floored), never stored.
- Company scoping via `BelongsToCompany`; non-global roles only see rows
  of their allowed companies. Absence is derived (no row on a scheduled
  workday), never stored, and never auto-deducted from leave.

## What it is NOT

- No shifts, overtime, late/early penalties, grace periods, or payroll.
- No multi-session days, no break splitting, no IN/OUT direction logic —
  today the model is deliberately first-in / last-out per day.
- No automatic leave deduction. An unexcused absence stays a visible fact
  until an HR user explicitly acts on it.
- No approval workflow and no per-shift source precedence yet: a `manual`
  row and a `biometric-attendance` row for the same day coexist and are both counted.
  A formal precedence rule must be written before payroll use.

## Ideas for future developers

1. **Shift definitions with night windows** — named shifts with start/end
   spanning midnight, so 22:00 → 06:00 pairs onto one logical day instead
   of being rejected.
2. **Break-aware, multi-session pairing** — split lunch/break punches into
   sessions instead of first/last, with per-session minutes.
3. **Advanced work-time rules** — overtime, late arrival, early leave, and
   grace periods driven by shift policy, computed at read time like
   `worked_minutes` (never stored).
4. **Payroll period lock** — freeze days under payroll processing so even
   delete + recreate is blocked inside a locked period.
5. **Absence review queue** — list scheduled workdays with neither a punch
   row nor approved leave, for HR follow-up. Needs calendar-aware workday
   detection (skip weekends and holidays) to avoid false positives.
6. **Correction reasons** — a mandatory note field on manual edits/deletes
   for audit quality.
7. **My Attendance (self-service)** — show employees their own present days
   next to leave requests (read-only, same queries as the widget).
8. **HR notifications with quick actions** — notify HR about review items;
   actions (e.g. deduct from annual leave balance when available) must be
   explicit, named human actions — never silent automatic deductions.
9. **Biometric pairing** — the sibling biometric plugin writes
   `source='biometric-attendance:<device-id>'` rows here via first/last punch pairing; keep that
   dependency one-way (`biometric → attendance → employees`).
