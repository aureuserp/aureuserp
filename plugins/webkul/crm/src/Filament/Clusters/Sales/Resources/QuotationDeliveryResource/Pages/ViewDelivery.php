<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages\ViewDelivery as BaseViewDelivery;

class ViewDelivery extends BaseViewDelivery
{
    protected static string $resource = QuotationDeliveryResource::class;
}
