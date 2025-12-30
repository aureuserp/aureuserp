<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages;

use Webkul\Account\Filament\Resources\TaxResource\Pages\ListTaxes as BaseListTaxes;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource;

class ListTaxes extends BaseListTaxes
{
    protected static string $resource = TaxResource::class;
}
