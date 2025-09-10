<?php

namespace Webkul\Employee\Filament\Resources\Departments\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Employee\Filament\Resources\Departments\DepartmentResource;

class CreateDepartment extends CreateRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('employees::filament/resources/department/pages/create-department.notification.title'))
            ->body(__('employees::filament/resources/department/pages/create-department.notification.body'));
    }
}
