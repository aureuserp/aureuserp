<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages;

use Webkul\Account\Filament\Resources\TaxResource\Pages\ViewTax as BaseViewTax;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource;

class ViewTax extends BaseViewTax
{
    protected static string $resource = TaxResource::class;
}
