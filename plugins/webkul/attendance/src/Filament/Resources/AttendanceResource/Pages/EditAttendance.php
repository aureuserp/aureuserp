<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Employee\Models\Employee;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    /**
     * Stored values are UTC; show them back on the employee's wall clock.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $timezone = ! empty($data['employee_id'])
            ? (Employee::query()->whereKey($data['employee_id'])->value('time_zone') ?: config('app.timezone'))
            : config('app.timezone');

        foreach (['check_in', 'check_out'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = Carbon::parse($data[$field], 'UTC')->setTimezone($timezone)->format('Y-m-d H:i:s');
            }
        }

        return $data;
    }

    /**
     * The row identity (employee + work day) is frozen: the fields are
     * disabled in the form, and any forged change is dropped here so the
     * stored times can never be silently re-based onto another zone.
     *
     * @see CreateAttendance::mutateFormDataBeforeCreate()
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['employee_id'] = $this->getRecord()->employee_id;
        $data['work_date'] = $this->getRecord()->work_date->format('Y-m-d');

        $timezone = Employee::query()->whereKey($data['employee_id'])->value('time_zone') ?: config('app.timezone');

        foreach (['check_in', 'check_out'] as $field) {
            if (! empty($data[$field])) {
                $data[$field] = Carbon::parse($data[$field], $timezone)->setTimezone('UTC')->format('Y-m-d H:i:s');
            }
        }

        return $data;
    }
}
