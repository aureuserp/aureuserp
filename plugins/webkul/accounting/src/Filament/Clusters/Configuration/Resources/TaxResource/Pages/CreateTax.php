<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages;

use Webkul\Account\Filament\Resources\TaxResource\Pages\CreateTax as BaseCreateTax;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource;

class CreateTax extends BaseCreateTax
{
    protected static string $resource = TaxResource::class;
}
