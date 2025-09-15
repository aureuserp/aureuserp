<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\LocationResource;
use Webkul\Inventory\Models\Location;

class ListLocations extends ListRecords
{
    protected static string $resource = LocationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('inventories::filament/clusters/configurations/resources/location/pages/list-locations.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function ($data) {
                    $user = Auth::user();

                    $data['creator_id'] = $user->id;

                    $data['company_id'] = $user->defaultCompany?->id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/location/pages/list-locations.header-actions.create.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/location/pages/list-locations.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getDefaultActiveTab(): string|int|null
    {
        return 'internal';
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(__('inventories::filament/clusters/configurations/resources/location/pages/list-locations.tabs.all'))
                ->badge(Location::count()),
            'internal' => Tab::make(__('inventories::filament/clusters/configurations/resources/location/pages/list-locations.tabs.internal'))
                ->badge(Location::where('type', LocationType::INTERNAL)->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('type', LocationType::INTERNAL);
                }),
            'customer' => Tab::make(__('inventories::filament/clusters/configurations/resources/location/pages/list-locations.tabs.customer'))
                ->badge(Location::where('type', LocationType::CUSTOMER)->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('type', LocationType::CUSTOMER);
                }),
            'production' => Tab::make(__('inventories::filament/clusters/configurations/resources/location/pages/list-locations.tabs.production'))
                ->badge(Location::where('type', LocationType::PRODUCTION)->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('type', LocationType::PRODUCTION);
                }),
            'vendor' => Tab::make(__('inventories::filament/clusters/configurations/resources/location/pages/list-locations.tabs.vendor'))
                ->badge(Location::where('type', LocationType::SUPPLIER)->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->where('type', LocationType::SUPPLIER);
                }),
            'archived' => Tab::make(__('inventories::filament/clusters/configurations/resources/location/pages/list-locations.tabs.archived'))
                ->badge(Location::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query) {
                    return $query->onlyTrashed();
                }),
        ];
    }
}
