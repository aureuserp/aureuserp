<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\PackageTypeResource;

class ListPackageTypes extends ListRecords
{
    protected static string $resource = PackageTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('inventories::filament/clusters/configurations/resources/package-type/pages/list-package-types.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function ($data) {
                    $user = Auth::user();

                    $data['creator_id'] = $user->id;

                    $data['company_id'] = $user->default_company_id;

                    return $data;
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/package-type/pages/list-package-types.header-actions.create.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/package-type/pages/list-package-types.header-actions.create.notification.body')),
                ),
        ];
    }
}
