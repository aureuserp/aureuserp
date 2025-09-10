<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroups\Pages;

use Webkul\Account\Filament\Resources\TaxGroups\Pages\ListTaxGroups as BaseListTaxGroups;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroups\TaxGroupResource;

class ListTaxGroups extends BaseListTaxGroups
{
    protected static string $resource = TaxGroupResource::class;
}
