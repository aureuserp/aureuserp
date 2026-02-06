<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Sale\Facades\SaleOrder;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateQuotation extends CreateRecord
{
    use HasRepeaterColumnManager;

    protected static string $resource = QuotationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
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
            ->title(__('sales::filament/clusters/orders/resources/quotation/pages/create-quotation.notification.title'))
            ->body(__('sales::filament/clusters/orders/resources/quotation/pages/create-quotation.notification.body'));
    }

    protected function afterCreate(): void
    {
        SaleOrder::computeSaleOrder($this->getRecord());
    }
}
