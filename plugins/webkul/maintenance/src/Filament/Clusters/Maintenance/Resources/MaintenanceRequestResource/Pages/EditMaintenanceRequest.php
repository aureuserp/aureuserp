<?php

namespace Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Maintenance\Filament\Clusters\Maintenance\Resources\MaintenanceRequestResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class EditMaintenanceRequest extends EditRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = MaintenanceRequestResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/edit-maintenance-request.notification.title'))
            ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/edit-maintenance-request.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/edit-maintenance-request.header-actions.delete.notification.title'))
                        ->body(__('maintenance::filament/clusters/maintenance/resources/maintenance-request/pages/edit-maintenance-request.header-actions.delete.notification.body')),
                ),
        ];
    }
}
