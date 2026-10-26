<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationDeliveryResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationDeliveryResource\Pages\ManageMoves as BaseManageMoves;

class ManageMoves extends BaseManageMoves
{
    protected static string $resource = QuotationDeliveryResource::class;
}
