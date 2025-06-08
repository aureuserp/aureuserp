<?php

namespace Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\TimeOff\Filament\Clusters\Reporting\Resources\ByEmployeeResource;

class EditByEmployee extends EditRecord
{
    protected static string $resource = ByEmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/clusters/reporting/resources/by-employee/edit-by-employee.header-actions.delete.notification.title'))
                        ->body(__('time-off::filament/clusters/reporting/resources/by-employee/edit-by-employee.header-actions.delete.notification.body'))
                ),
        ];
    }
}
