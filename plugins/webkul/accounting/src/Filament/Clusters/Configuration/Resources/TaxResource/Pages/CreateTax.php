<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource;
use Webkul\Account\Filament\Resources\TaxResource\Pages\CreateTax as BaseCreateTax;

class CreateTax extends BaseCreateTax
{
    protected static string $resource = TaxResource::class;
}
