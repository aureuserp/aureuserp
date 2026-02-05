<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\CreateCurrency as BaseCreateCurrency;

class CreateCurrency extends BaseCreateCurrency
{
    protected static string $resource = CurrencyResource::class;
}
