<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroups\Pages;

use Webkul\Account\Filament\Resources\TaxGroups\Pages\EditTaxGroup as BaseEditTaxGroup;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\TaxGroups\TaxGroupResource;

class EditTaxGroup extends BaseEditTaxGroup
{
    protected static string $resource = TaxGroupResource::class;
}
