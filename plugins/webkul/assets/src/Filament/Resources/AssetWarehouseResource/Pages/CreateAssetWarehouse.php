<?php

namespace Webkul\Asset\Filament\Resources\AssetWarehouseResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class CreateAssetWarehouse extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = AssetWarehouseResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('assets::filament/resources/asset-warehouse.pages.create.notification.title'))
            ->body(__('assets::filament/resources/asset-warehouse.pages.create.notification.body'));
    }
}
