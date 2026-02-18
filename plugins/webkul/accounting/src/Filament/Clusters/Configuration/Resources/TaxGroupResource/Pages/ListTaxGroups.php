<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxGroupResource\Pages;

use Webkul\Account\Filament\Resources\TaxGroupResource\Pages\ListTaxGroups as BaseListTaxGroups;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxGroupResource;

class ListTaxGroups extends BaseListTaxGroups
{
    protected static string $resource = TaxGroupResource::class;
}
