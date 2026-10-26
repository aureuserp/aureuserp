<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages\EditDelivery as BaseEditDelivery;

class EditDelivery extends BaseEditDelivery
{
    protected static string $resource = QuotationDeliveryResource::class;
}
