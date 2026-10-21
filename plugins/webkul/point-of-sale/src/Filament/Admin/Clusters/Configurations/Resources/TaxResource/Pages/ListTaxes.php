<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource\Pages;

use Webkul\Account\Filament\Resources\TaxResource\Pages\ListTaxes as BaseListTaxes;
use Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\TaxResource;

class ListTaxes extends BaseListTaxes
{
    protected static string $resource = TaxResource::class;
}
