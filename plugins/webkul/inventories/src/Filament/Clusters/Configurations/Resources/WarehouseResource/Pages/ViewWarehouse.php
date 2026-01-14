<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewWarehouse extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = WarehouseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/view-warehouse.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/warehouse/pages/view-warehouse.header-actions.delete.notification.body')),
                ),
        ];
    }
}
