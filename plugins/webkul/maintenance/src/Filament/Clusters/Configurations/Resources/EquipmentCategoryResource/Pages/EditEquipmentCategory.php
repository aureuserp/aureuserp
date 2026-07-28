<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource;

class EditEquipmentCategory extends EditRecord
{
    protected static string $resource = EquipmentCategoryResource::class;

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
            ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category/pages/edit-equipment-category.notification.title'))
            ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category/pages/edit-equipment-category.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category/pages/edit-equipment-category.header-actions.delete.notification.title'))
                        ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category/pages/edit-equipment-category.header-actions.delete.notification.body')),
                ),
        ];
    }
}
