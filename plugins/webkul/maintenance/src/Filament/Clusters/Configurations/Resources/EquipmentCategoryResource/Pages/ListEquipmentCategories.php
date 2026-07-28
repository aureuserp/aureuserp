<?php

namespace Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Maintenance\Filament\Clusters\Configurations\Resources\EquipmentCategoryResource;

class ListEquipmentCategories extends ListRecords
{
    protected static string $resource = EquipmentCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('maintenance::filament/clusters/configurations/resources/equipment-category/pages/list-equipment-categories.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/clusters/configurations/resources/equipment-category/pages/list-equipment-categories.header-actions.create.notification.title'))
                        ->body(__('maintenance::filament/clusters/configurations/resources/equipment-category/pages/list-equipment-categories.header-actions.create.notification.body')),
                ),
        ];
    }
}
