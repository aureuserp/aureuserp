<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource\Pages;

use Webkul\Account\Filament\Resources\TaxResource\Pages\EditTax as BaseEditTax;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource;

class EditTax extends BaseEditTax
{
    protected static string $resource = TaxResource::class;
}
