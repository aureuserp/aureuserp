<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\TaxResource;
use Webkul\Account\Filament\Resources\TaxResource\Pages\EditTax as BaseEditTax;

class EditTax extends BaseEditTax
{
    protected static string $resource = TaxResource::class;
}
