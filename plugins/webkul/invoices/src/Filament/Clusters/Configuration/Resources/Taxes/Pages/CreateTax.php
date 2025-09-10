<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\Pages;

use Webkul\Account\Filament\Resources\Taxes\Pages\CreateTax as BaseCreateTax;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\TaxResource;

class CreateTax extends BaseCreateTax
{
    protected static string $resource = TaxResource::class;
}
