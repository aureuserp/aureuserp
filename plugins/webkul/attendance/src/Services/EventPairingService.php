<?php

namespace Webkul\Attendance\Services;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceEvent;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Scopes\CompanyScope;

/**
 * Rows are a projection of events: for one employee and one writer slug,
 * the rows of the affected time window are rebuilt from the events and
 * reconciled in place. Re-running with the same events yields the same
 * rows. Manual rows are never read or written here.
 */
class EventPairingService
{
    /**
     * Punches closer than this to the previously accepted punch are
     * accidental double scans: linked to the session, never moving it.
     */
    public const DUPLICATE_PUNCH_SECONDS = 120;

    /**
     * @return array{events: int, rows: list<int>}
     */
    public function rebuild(Employee $employee, string $writer): array
    {
        return DB::transaction(function () use ($employee, $writer): array {
            $pending = $this->writerEvents($employee, $writer, fn (Builder $query) => $query->whereNull('attendance_id'));

            if ($pending->isEmpty()) {
                return ['events' => 0, 'rows' => []];
            }

            $earliestPending = $pending->first()->getRawOriginal('punched_at');

            $windowStart = Carbon::parse($earliestPending, 'UTC')
                ->subHours(Attendance::MAX_SHIFT_HOURS)
                ->format('Y-m-d H:i:s');

            $touching = $this->writerRows($employee, $writer, fn (Builder $query) => $query->where(function (Builder $query) use ($windowStart): void {
                $query->where('check_out', '>=', $windowStart)
                    ->orWhere('check_in', '>=', $windowStart);
            }));

            $rebuildFrom = $touching
                ->map(fn (Attendance $row): string => $row->getRawOriginal('check_in'))
                ->push($earliestPending)
                ->min();

            $rows = $this->writerRows($employee, $writer, fn (Builder $query) => $query->where('check_in', '>=', $rebuildFrom));

            $rowIds = $rows->modelKeys();

            $events = $this->writerEvents($employee, $writer, fn (Builder $query) => $query->where(function (Builder $query) use ($rowIds): void {
                $query->whereNull('attendance_id')
                    ->orWhereIn('attendance_id', $rowIds);
            }));

            return [
                'events' => $pending->count(),
                'rows'   => $this->reconcile($employee, $rows, $this->sessionize($events)),
            ];
        });
    }

    /**
     * @param  Collection<int, AttendanceEvent>  $events
     * @return list<array{check_in: Carbon, check_out: ?Carbon, events: list<AttendanceEvent>}>
     */
    protected function sessionize(Collection $events): array
    {
        $sessions = [];
        $current = null;
        $lastAccepted = null;

        foreach ($events as $event) {
            $punch = Carbon::parse($event->getRawOriginal('punched_at'), 'UTC');

            if ($current !== null && $lastAccepted->diffInSeconds($punch) < self::DUPLICATE_PUNCH_SECONDS) {
                $current['events'][] = $event;

                continue;
            }

            if ($current !== null && Attendance::isValidCheckout($current['check_in'], $punch)) {
                $current['check_out'] = $punch;
                $current['events'][] = $event;
                $lastAccepted = $punch;

                continue;
            }

            if ($current !== null) {
                $sessions[] = $current;
            }

            $current = ['check_in' => $punch, 'check_out' => null, 'events' => [$event]];
            $lastAccepted = $punch;
        }

        if ($current !== null) {
            $sessions[] = $current;
        }

        return $sessions;
    }

    /**
     * @param  Collection<int, Attendance>  $rows
     * @param  list<array{check_in: Carbon, check_out: ?Carbon, events: list<AttendanceEvent>}>  $sessions
     * @return list<int>
     */
    protected function reconcile(Employee $employee, Collection $rows, array $sessions): array
    {
        $timezone = $employee->time_zone ?: config('app.timezone');
        $rowIds = [];

        foreach ($sessions as $index => $session) {
            $row = $rows->get($index) ?? new Attendance;
            $first = $session['events'][0];
            $checkOut = $session['check_out']?->format('Y-m-d H:i:s');

            if (
                $checkOut === null
                && $row->exists
                && $row->getRawOriginal('check_out') !== null
                && Attendance::isValidCheckout($session['check_in'], Carbon::parse($row->getRawOriginal('check_out'), 'UTC'))
            ) {
                $checkOut = $row->getRawOriginal('check_out');
            }

            $row->fill([
                'employee_id'  => $employee->getKey(),
                'work_date'    => $session['check_in']->copy()->setTimezone($timezone)->toDateString(),
                'check_in'     => $session['check_in']->format('Y-m-d H:i:s'),
                'check_out'    => $checkOut,
                'source'       => $first->source,
                'source_label' => $first->source_label,
                'company_id'   => $first->company_id ?? $employee->company_id,
            ]);

            $row->save();

            AttendanceEvent::query()
                ->withoutGlobalScope(CompanyScope::class)
                ->whereKey(array_map(fn (AttendanceEvent $event): int => $event->getKey(), $session['events']))
                ->update(['attendance_id' => $row->getKey()]);

            $rowIds[] = $row->getKey();
        }

        $rows->slice(count($sessions))->each(fn (Attendance $row) => $row->delete());

        return $rowIds;
    }

    /**
     * @param  Closure(Builder): mixed  $constraint
     * @return Collection<int, AttendanceEvent>
     */
    protected function writerEvents(Employee $employee, string $writer, Closure $constraint): Collection
    {
        return AttendanceEvent::query()
            ->withoutGlobalScope(CompanyScope::class)
            ->where('employee_id', $employee->getKey())
            ->tap($constraint)
            ->orderBy('punched_at')
            ->orderBy('id')
            ->get()
            ->filter(fn (AttendanceEvent $event): bool => Attendance::sourceWriter($event->source) === $writer)
            ->values();
    }

    /**
     * @param  Closure(Builder): mixed  $constraint
     * @return Collection<int, Attendance>
     */
    protected function writerRows(Employee $employee, string $writer, Closure $constraint): Collection
    {
        return Attendance::query()
            ->withoutGlobalScope(CompanyScope::class)
            ->where('employee_id', $employee->getKey())
            ->where('source', '!=', Attendance::SOURCE_MANUAL)
            ->tap($constraint)
            ->orderBy('check_in')
            ->orderBy('id')
            ->get()
            ->filter(fn (Attendance $row): bool => Attendance::sourceWriter($row->source) === $writer)
            ->values();
    }
}
