<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\CurrencyResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\CreateCurrency as BaseCreateCurrency;

class CreateCurrency extends BaseCreateCurrency
{
    protected static string $resource = CurrencyResource::class;
}
