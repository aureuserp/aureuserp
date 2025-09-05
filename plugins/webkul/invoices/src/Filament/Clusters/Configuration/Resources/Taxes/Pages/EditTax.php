<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\Pages;

use Webkul\Account\Filament\Resources\Taxes\Pages\EditTax as BaseEditTax;
use Webkul\Invoice\Filament\Clusters\Configuration\Resources\Taxes\TaxResource;

class EditTax extends BaseEditTax
{
    protected static string $resource = TaxResource::class;
}
