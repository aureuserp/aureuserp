<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource;
use Webkul\Inventory\Models\Location;

class CreateLocation extends CreateRecord
{
    protected static string $resource = LocationResource::class;

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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('inventories::filament/clusters/configurations/resources/location/pages/create-location.notification.title'))
            ->body(__('inventories::filament/clusters/configurations/resources/location/pages/create-location.notification.body'));
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['creator_id'] = Auth::id();

        $parentLocation = Location::find($data['parent_id']);

        $data['warehouse_id'] = $parentLocation?->warehouse_id;

        $data['next_inventory_date'] = isset($data['cyclic_inventory_frequency']) && $data['cyclic_inventory_frequency']
            ? now()->addDays((int) $data['cyclic_inventory_frequency'])
            : null;

        return $data;
    }
}
