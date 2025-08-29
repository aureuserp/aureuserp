<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Pages\CreateOrder;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Quotations\QuotationResource;

class CreateQuotation extends CreateOrder
{
    protected static string $resource = QuotationResource::class;
}
