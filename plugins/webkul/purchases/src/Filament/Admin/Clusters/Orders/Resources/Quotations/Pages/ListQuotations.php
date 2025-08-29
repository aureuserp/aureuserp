<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Pages\ListOrders;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\QuotationResource;

class ListQuotations extends ListOrders
{
    protected static string $resource = QuotationResource::class;
}
