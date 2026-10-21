<?php

namespace Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource\Pages;

use Webkul\Crm\Filament\Clusters\Sales\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\CreateQuotation as BaseCreateQuotation;

class CreateQuotation extends BaseCreateQuotation
{
    protected static string $resource = QuotationResource::class;
}
