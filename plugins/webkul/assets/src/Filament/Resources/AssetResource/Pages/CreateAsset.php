<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class CreateAsset extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = AssetResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('assets::filament/resources/asset.pages.create.notification.title'))
            ->body(__('assets::filament/resources/asset.pages.create.notification.body'));
    }
}
