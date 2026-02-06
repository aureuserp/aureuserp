<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Purchase\Facades\PurchaseOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateOrder extends CreateRecord
{
    use HasRepeaterColumnManager;

    protected static string $resource = OrderResource::class;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('purchases::filament/admin/clusters/orders/resources/order/pages/create-order.notification.title'))
            ->body(__('purchases::filament/admin/clusters/orders/resources/order/pages/create-order.notification.body'));
    }

    protected function afterCreate(): void
    {
        PurchaseOrder::computePurchaseOrder($this->getRecord());
    }
}
