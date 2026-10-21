<?php

namespace Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\TimeOff\Filament\Clusters\Configurations\Resources\LeaveTypeResource;
use Webkul\TimeOff\Models\Leave;

class EditLeaveType extends EditRecord
{
    protected static string $resource = LeaveTypeResource::class;

    protected function beforeSave(): void
    {
        $allocationChanged = array_key_exists('requires_allocation', $this->data)
            && $this->data['requires_allocation'] !== $this->record->requires_allocation;

        if ($allocationChanged && Leave::where('holiday_status_id', $this->record->id)->exists()) {
            Notification::make()
                ->danger()
                ->title(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.notification.allocation-locked.title'))
                ->body(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.notification.allocation-locked.body'))
                ->send();

            $this->halt();
        }
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.notification.title'))
            ->body(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.header-actions.delete.notification.title'))
                        ->body(__('time-off::filament/clusters/configurations/resources/leave-type/pages/edit-leave-type.header-actions.delete.notification.body'))
                ),
        ];
    }
}
