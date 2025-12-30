<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\CurrencyResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\EditCurrency as BaseEditCurrency;

class EditCurrency extends BaseEditCurrency
{
    protected static string $resource = CurrencyResource::class;
}
