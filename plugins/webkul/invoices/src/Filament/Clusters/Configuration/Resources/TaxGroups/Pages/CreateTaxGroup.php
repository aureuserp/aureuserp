<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroups\Pages;

use Webkul\Account\Filament\Resources\TaxGroups\Pages\CreateTaxGroup as BaseCreateTaxGroup;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroups\TaxGroupResource;

class CreateTaxGroup extends BaseCreateTaxGroup
{
    protected static string $resource = TaxGroupResource::class;
}
