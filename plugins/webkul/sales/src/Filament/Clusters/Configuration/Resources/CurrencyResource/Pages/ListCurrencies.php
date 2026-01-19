<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\CurrencyResource\Pages;

use Webkul\Sale\Filament\Clusters\Configuration\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ListCurrencies as BaseListCurrencies;

class ListCurrencies extends BaseListCurrencies
{
    protected static string $resource = CurrencyResource::class;
}
