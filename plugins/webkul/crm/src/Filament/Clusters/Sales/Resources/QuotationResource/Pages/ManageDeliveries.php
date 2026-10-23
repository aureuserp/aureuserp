<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ManageDeliveries as BaseManageDeliveries;

class ManageDeliveries extends BaseManageDeliveries
{
    protected static string $resource = QuotationResource::class;
}
