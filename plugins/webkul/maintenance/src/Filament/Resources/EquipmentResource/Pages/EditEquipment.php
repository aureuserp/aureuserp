<?php

namespace Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Maintenance\Filament\Resources\EquipmentResource;

class EditEquipment extends EditRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('maintenance::filament/resources/equipment/pages/edit-equipment.notification.title'))
            ->body(__('maintenance::filament/resources/equipment/pages/edit-equipment.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/resources/equipment/pages/edit-equipment.header-actions.delete.notification.title'))
                        ->body(__('maintenance::filament/resources/equipment/pages/edit-equipment.header-actions.delete.notification.body')),
                ),
        ];
    }
}
