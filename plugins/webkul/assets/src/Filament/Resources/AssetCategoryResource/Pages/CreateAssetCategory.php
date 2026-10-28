<?php

namespace Webkul\Asset\Filament\Resources\AssetCategoryResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Asset\Filament\Resources\AssetCategoryResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class CreateAssetCategory extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = AssetCategoryResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('assets::filament/resources/category.pages.create.notification.title'))
            ->body(__('assets::filament/resources/category.pages.create.notification.body'));
    }
}
