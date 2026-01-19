<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ListCurrencies as BaseListCurrencies;

class ListCurrencies extends BaseListCurrencies
{
    protected static string $resource = CurrencyResource::class;
}
