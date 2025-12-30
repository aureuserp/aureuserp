<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages;

use Webkul\Account\Filament\Resources\TaxResource\Pages\EditTax as BaseEditTax;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource;

class EditTax extends BaseEditTax
{
    protected static string $resource = TaxResource::class;
}
