<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Pages\ViewOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\QuotationResource;

class ViewQuotation extends ViewOrder
{
    protected static string $resource = QuotationResource::class;
}
