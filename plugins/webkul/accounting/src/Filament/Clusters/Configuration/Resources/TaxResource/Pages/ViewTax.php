<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource;
use Webkul\Account\Filament\Resources\TaxResource\Pages\ViewTax as BaseViewTax;

class ViewTax extends BaseViewTax
{
    protected static string $resource = TaxResource::class;
}
