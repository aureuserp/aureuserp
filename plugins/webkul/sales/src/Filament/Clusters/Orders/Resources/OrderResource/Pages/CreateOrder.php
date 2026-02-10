<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource\Pages;

use Webkul\Sale\Filament\Clusters\Orders\Resources\OrderResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\CreateQuotation as BaseCreateOrders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;

class CreateOrder extends BaseCreateOrders
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return QuotationResource::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
