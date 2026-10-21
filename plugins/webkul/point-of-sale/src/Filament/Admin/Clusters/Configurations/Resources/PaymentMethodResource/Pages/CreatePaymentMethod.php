<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\CreateRecord;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\PaymentMethodResource;

class CreatePaymentMethod extends CreateRecord
{
    protected static string $resource = PaymentMethodResource::class;

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

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/create-payment-method.notification.success.title'))
            ->body(__('point-of-sale::filament/admin/clusters/configurations/resources/payment-method/pages/create-payment-method.notification.success.body'));
    }
}
