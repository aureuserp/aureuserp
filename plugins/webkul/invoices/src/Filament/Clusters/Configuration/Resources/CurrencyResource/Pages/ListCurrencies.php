<?php

namespace Webkul\Invoice\Filament\Clusters\Configuration\Resources\CurrencyResource\Pages;

use Webkul\Invoice\Filament\Clusters\Configuration\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ListCurrencies as BaseListCurrencies;

class ListCurrencies extends BaseListCurrencies
{
    protected static string $resource = CurrencyResource::class;
}
