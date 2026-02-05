<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\CurrencyResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ViewCurrency as BaseViewCurrency;

class ViewCurrency extends BaseViewCurrency
{
    protected static string $resource = CurrencyResource::class;
}
