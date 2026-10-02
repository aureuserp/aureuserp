<?php

namespace Webkul\Attendance\Events;

use InvalidArgumentException;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceEvent;
use Webkul\Attendance\Writers\WriterRegistry;
use Webkul\Employee\Models\Employee;

class EventGateway
{
    public static function push(int $employeeId, string $punchedAtUtc, int $direction, string $source, string $sourceRef, ?string $sourceLabel = null): AttendanceEvent
    {
        if (! in_array($direction, [AttendanceEvent::DIRECTION_OUT, AttendanceEvent::DIRECTION_UNKNOWN, AttendanceEvent::DIRECTION_IN], true)) {
            throw new InvalidArgumentException("Invalid attendance direction [{$direction}].");
        }

        $writer = Attendance::sourceWriter($source);

        if ($writer === null || ! WriterRegistry::isRegistered($writer)) {
            throw new InvalidArgumentException("Unknown attendance source [{$source}].");
        }

        $employee = Employee::query()->findOrFail($employeeId);

        return AttendanceEvent::firstOrCreate(
            [
                'source'     => $source,
                'source_ref' => $sourceRef,
            ],
            [
                'employee_id'  => $employee->getKey(),
                'punched_at'   => $punchedAtUtc,
                'direction'    => $direction,
                'source_label' => $sourceLabel,
                'company_id'   => $employee->company_id,
            ]
        );
    }
}
