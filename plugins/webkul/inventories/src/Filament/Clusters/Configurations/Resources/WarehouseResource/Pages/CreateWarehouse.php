<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\WarehouseResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Models\Rule;

class CreateWarehouse extends CreateRecord
{
    protected static string $resource = WarehouseResource::class;

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected array $routeIds = [];

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/warehouse/pages/create-warehouse.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/warehouse/pages/create-warehouse.notification.body'));
    }

    protected function afterCreate(): void
    {
        Location::withTrashed()->whereIn('id', [
            $this->getRecord()->view_location_id,
            $this->getRecord()->lot_stock_location_id,
            $this->getRecord()->input_stock_location_id,
            $this->getRecord()->qc_stock_location_id,
            $this->getRecord()->output_stock_location_id,
            $this->getRecord()->pack_stock_location_id,
        ])->update(['warehouse_id' => $this->getRecord()->id]);

        OperationType::withTrashed()->whereIn('id', [
            $this->getRecord()->in_type_id,
            $this->getRecord()->out_type_id,
            $this->getRecord()->pick_type_id,
            $this->getRecord()->pack_type_id,
            $this->getRecord()->qc_type_id,
            $this->getRecord()->store_type_id,
            $this->getRecord()->internal_type_id,
            $this->getRecord()->xdock_type_id,
        ])->update(['warehouse_id' => $this->getRecord()->id]);

        $this->getRecord()->routes()->sync([
            $this->getRecord()->reception_route_id,
            $this->getRecord()->delivery_route_id,
            $this->getRecord()->crossdock_route_id,
        ]);

        Rule::withTrashed()->whereIn('id', $this->routeIds)->update(['warehouse_id' => $this->getRecord()->id]);
    }
}
