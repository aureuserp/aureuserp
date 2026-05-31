<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\OperationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\OperationTypeResource;
use Webkul\Inventory\Models\Location;

class CreateOperationType extends CreateRecord
{
    protected static string $resource = OperationTypeResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/operation-type/pages/create-operation-type.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/operation-type/pages/create-operation-type.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $type = $data['type'];
        $warehouseId = $data['warehouse_id'];

        $data['source_location_id'] ??= match ($type) {
            OperationType::INCOMING => Location::where('type', LocationType::SUPPLIER->value)->first()?->id,

            OperationType::OUTGOING => Location::where('is_replenish', 1)
                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                ->first()?->id,

            OperationType::INTERNAL => Location::where('is_replenish', 1)
                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                ->first()?->id,

            OperationType::DROPSHIP => Location::where('type', LocationType::SUPPLIER->value)->first()?->id,
            default                 => null,
        };

        $data['destination_location_id'] ??= match ($type) {
            OperationType::INCOMING => Location::where('is_replenish', 1)
                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                ->first()?->id,

            OperationType::OUTGOING => Location::where('type', LocationType::CUSTOMER->value)->first()?->id,

            OperationType::INTERNAL => Location::where('is_replenish', 1)
                ->when($warehouseId, fn ($query) => $query->where('warehouse_id', $warehouseId))
                ->first()?->id,

            OperationType::DROPSHIP => Location::where('type', LocationType::CUSTOMER->value)->first()?->id,

            default => null,
        };

        return $data;
    }
}
