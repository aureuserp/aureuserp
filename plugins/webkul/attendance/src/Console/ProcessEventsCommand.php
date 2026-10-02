<?php

namespace Webkul\Attendance\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceEvent;

class ProcessEventsCommand extends Command
{
    protected $signature = 'attendance:process-events';

    protected $description = 'Pair raw attendance events into daily rows (idempotent).';

    public function handle(): int
    {
        // V1 single-pass pairing; performance work is deferred.
        $events = AttendanceEvent::query()
            ->whereNull('attendance_id')
            ->orderBy('punched_at')
            ->orderBy('id')
            ->with('employee')
            ->get();

        $processed = 0;
        $rowIds = [];

        foreach ($events->groupBy(fn (AttendanceEvent $event): string => $event->employee_id.'|'.(Attendance::sourceWriter($event->source) ?? $event->source)) as $group) {
            foreach ($group->sortBy(fn (AttendanceEvent $event): string => $event->getRawOriginal('punched_at').'#'.$event->getKey()) as $event) {
                $employee = $event->employee;

                if (! $employee) {
                    continue;
                }

                $timezone = $employee->time_zone ?: config('app.timezone');
                $punchedRaw = $event->getRawOriginal('punched_at');
                $punchedUtc = Carbon::parse($punchedRaw, 'UTC')->format('Y-m-d H:i:s');
                $eventDate = Carbon::parse($punchedRaw, 'UTC')->setTimezone($timezone)->toDateString();
                $writer = Attendance::sourceWriter($event->source);
                $punch = Carbon::parse($punchedRaw, 'UTC');

                $row = Attendance::query()
                    ->where('employee_id', $event->employee_id)
                    ->whereNull('check_out')
                    ->orderBy('work_date', 'desc')
                    ->orderBy('check_in', 'desc')
                    ->orderBy('id', 'desc')
                    ->get()
                    ->first(function (Attendance $candidate) use ($writer, $punch): bool {
                        if (Attendance::sourceWriter($candidate->source) !== $writer) {
                            return false;
                        }

                        return Attendance::isValidCheckout(Carbon::parse($candidate->getRawOriginal('check_in'), 'UTC'), $punch);
                    });

                if ($row) {
                    $row->check_out = $punchedUtc;
                    $row->save();
                } else {
                    $existing = Attendance::query()
                        ->where('employee_id', $event->employee_id)
                        ->where('work_date', $eventDate)
                        ->where('source', $event->source)
                        ->first();

                    if ($existing) {
                        $checkInRaw = $existing->getRawOriginal('check_in');

                        if ($punchedUtc >= $checkInRaw) {
                            $existing->check_out = $punchedUtc;
                            $existing->save();
                        }

                        $row = $existing;
                    } else {
                        $row = Attendance::create([
                            'employee_id'  => $event->employee_id,
                            'work_date'    => $eventDate,
                            'check_in'     => $punchedUtc,
                            'check_out'    => null,
                            'source'       => $event->source,
                            'source_label' => $event->source_label,
                            'company_id'   => $event->company_id ?? $employee->company_id,
                        ]);
                    }
                }

                $event->attendance_id = $row->getKey();
                $event->save();

                $processed++;
                $rowIds[$row->getKey()] = true;
            }
        }

        $rows = Attendance::query()->whereIn('id', array_keys($rowIds))->get();
        $open = $rows->whereNull('check_out')->count();

        $this->info("Processed {$processed} events into ".count($rowIds)." rows ({$open} open).");

        return self::SUCCESS;
    }
}
