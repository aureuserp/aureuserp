<?php

namespace Webkul\Asset\Filament\Resources\AssetSiteResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class CreateAssetSite extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = AssetSiteResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('assets::filament/resources/asset-site.pages.create.notification.title'))
            ->body(__('assets::filament/resources/asset-site.pages.create.notification.body'));
    }
}
