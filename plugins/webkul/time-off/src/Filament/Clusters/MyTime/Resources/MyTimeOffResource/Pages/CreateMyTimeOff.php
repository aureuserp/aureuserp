<?php

namespace Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Models\Employee;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\MyTime\Resources\MyTimeOffResource;

class CreateMyTimeOff extends CreateRecord
{
    protected static string $resource = MyTimeOffResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.success.title'))
            ->body(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.success.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->first();

        if (! $employee) {
            Notification::make()
                ->warning()
                ->title(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.warning.title'))
                ->body(__('time-off::filament/clusters/my-time/resources/my-time-off/pages/create-time-off.notification.warning.body'))
                ->send();

            $this->halt();

            return $data;
        }

        $data['employee_id'] = $employee->id;

        $data['department_id'] = $employee->department?->id;

        if ($employee->calendar) {
            $data['calendar_id'] = $employee->calendar->id;

            $data['number_of_hours'] = $employee->calendar->hours_per_day;
        }

        $user = $employee?->user;

        if ($user) {
            $data['user_id'] = $user->id;

            $data['company_id'] = $user->default_company_id;

            $data['employee_company_id'] = $user->default_company_id;
        }

        if (isset($data['request_unit_half'])) {
            $data['duration_display'] = '0.5 day';

            $data['number_of_days'] = 0.5;
        } else {
            $startDate = Carbon::parse($data['request_date_from']);

            $endDate = $data['request_date_to'] ? Carbon::parse($data['request_date_to']) : $startDate;

            $data['duration_display'] = $startDate->diffInDays($endDate) + 1 .' day(s)';

            $data['number_of_days'] = $startDate->diffInDays($endDate) + 1;
        }

        $data['creator_id'] = $user->id;

        $data['state'] = State::CONFIRM->value;

        $data['date_from'] = $data['request_date_from'];

        $data['date_to'] = $data['request_date_to'];

        return $data;
    }
}
