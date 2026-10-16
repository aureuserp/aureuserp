<?php

namespace Webkul\Attendance\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceEvent;
use Webkul\Attendance\Services\EventPairingService;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Scopes\CompanyScope;

class ProcessEventsCommand extends Command
{
    public const LOCK_NAME = 'attendance:process-events';

    protected $signature = 'attendance:process-events';

    protected $description = 'Rebuild attendance rows from raw events (idempotent, safe to re-run).';

    public function handle(EventPairingService $pairing): int
    {
        $lock = Cache::lock(self::LOCK_NAME, 600);

        if (! $lock->get()) {
            $this->warn('Another pairing run is in progress. Skipping.');

            return self::SUCCESS;
        }

        try {
            $groups = AttendanceEvent::query()
                ->withoutGlobalScope(CompanyScope::class)
                ->whereNull('attendance_id')
                ->select(['employee_id', 'source'])
                ->distinct()
                ->get()
                ->map(fn (AttendanceEvent $event): array => [
                    'employee_id' => $event->employee_id,
                    'writer'      => Attendance::sourceWriter($event->source),
                ])
                ->filter(fn (array $group): bool => $group['writer'] !== null)
                ->unique(fn (array $group): string => $group['employee_id'].'|'.$group['writer']);

            $employees = Employee::query()
                ->whereKey($groups->pluck('employee_id')->unique()->all())
                ->get()
                ->keyBy('id');

            $processed = 0;
            $rowIds = [];

            foreach ($groups as $group) {
                $employee = $employees->get($group['employee_id']);

                if (! $employee) {
                    continue;
                }

                $result = $pairing->rebuild($employee, $group['writer']);

                $processed += $result['events'];

                foreach ($result['rows'] as $rowId) {
                    $rowIds[$rowId] = true;
                }
            }

            $open = Attendance::query()
                ->withoutGlobalScope(CompanyScope::class)
                ->whereKey(array_keys($rowIds))
                ->whereNull('check_out')
                ->count();

            $this->info("Processed {$processed} events into ".count($rowIds)." rows ({$open} open).");

            return self::SUCCESS;
        } finally {
            $lock->release();
        }
    }
}
