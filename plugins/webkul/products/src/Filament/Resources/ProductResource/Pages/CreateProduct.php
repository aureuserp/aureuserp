<?php

namespace Webkul\Product\Filament\Resources\ProductResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Product\Filament\Resources\ProductResource;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;

class CreateProduct extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected static string $resource = ProductResource::class;

    protected ?bool $hasDatabaseTransactions = true;

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
            ->title(__('products::filament/resources/product/pages/create-product.notification.title'))
            ->body(__('products::filament/resources/product/pages/create-product.notification.body'));
    }
}
