<?php

namespace Webkul\Employee\Filament\Resources\Employees\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Filament\Resources\Employees\EmployeeResource;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('employees::filament/resources/employee/pages/create-employee.notification.title'))
            ->body(__('employees::filament/resources/employee/pages/create-employee.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return [
            ...$data,
            'creator_id' => Auth::user()->id,
        ];
    }
}
