<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ViewCurrency as BaseViewCurrency;

class ViewCurrency extends BaseViewCurrency
{
    protected static string $resource = CurrencyResource::class;
}
