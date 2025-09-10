<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroups\Pages;

use Webkul\Account\Filament\Resources\TaxGroups\Pages\ViewTaxGroup as BaseViewTaxGroup;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroups\TaxGroupResource;

class ViewTaxGroup extends BaseViewTaxGroup
{
    protected static string $resource = TaxGroupResource::class;
}
