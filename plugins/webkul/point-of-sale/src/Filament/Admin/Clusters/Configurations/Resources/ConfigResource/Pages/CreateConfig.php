<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource;

class CreateConfig extends CreateRecord
{
    protected static string $resource = ConfigResource::class;

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

    protected function afterCreate(): void
    {
        $this->getRecord()->ensureDefaultPaymentMethods();

        $this->getRecord()->syncRestaurantFloors();
    }

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/create-config.notification.success.title'))
            ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/config/pages/create-config.notification.success.body'));
    }
}
