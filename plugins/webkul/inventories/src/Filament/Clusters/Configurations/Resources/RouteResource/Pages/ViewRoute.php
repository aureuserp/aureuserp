<?php

namespace Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Inventory\Filament\Clusters\Configurations\Resources\RouteResource;

class ViewRoute extends ViewRecord
{
    protected static string $resource = RouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('inventories::filament/clusters/configurations/resources/route/pages/view-route.header-actions.delete.notification.title'))
                        ->body(__('inventories::filament/clusters/configurations/resources/route/pages/view-route.header-actions.delete.notification.body')),
                ),
        ];
    }
}
