<?php

namespace Webkul\Support\Filament\Resources\UOMCategoryResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Support\Filament\Resources\UOMCategoryResource;

class CreateUOMCategory extends CreateRecord
{
    protected static string $resource = UOMCategoryResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('support::filament/resources/uom-category/pages/create-uom-category.notification.title'))
            ->body(__('support::filament/resources/uom-category/pages/create-uom-category.notification.body'));
    }
}
