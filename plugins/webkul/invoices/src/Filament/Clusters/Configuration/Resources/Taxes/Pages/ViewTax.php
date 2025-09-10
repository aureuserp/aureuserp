<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\Pages;

use Webkul\Account\Filament\Resources\Taxes\Pages\ViewTax as BaseViewTax;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\TaxResource;

class ViewTax extends BaseViewTax
{
    protected static string $resource = TaxResource::class;
}
