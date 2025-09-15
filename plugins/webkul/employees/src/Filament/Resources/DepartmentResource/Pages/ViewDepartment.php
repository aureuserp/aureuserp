<?php

namespace Webkul\Employee\Filament\Resources\DepartmentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions as ChatterActions;
use Webkul\Employee\Filament\Resources\DepartmentResource;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterActions\ChatterAction::make()
                ->setResource(static::$resource),
            EditAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/resources/department/pages/view-department.header-actions.delete.notification.title'))
                        ->body(__('employees::filament/resources/department/pages/view-department.header-actions.delete.notification.body')),
                ),
        ];
    }
}
