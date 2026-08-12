<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource;

class CreateEquipmentCategory extends CreateRecord
{
    protected static string $resource = EquipmentCategoryResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category/pages/create-equipment-category.notification.title'))
            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category/pages/create-equipment-category.notification.body'));
    }
}
