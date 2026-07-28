<?php

namespace Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Maintenance\Filament\Resources\EquipmentResource;

class CreateEquipment extends CreateRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('maintenance::filament/resources/equipment/pages/create-equipment.notification.title'))
            ->body(__('maintenance::filament/resources/equipment/pages/create-equipment.notification.body'));
    }
}
