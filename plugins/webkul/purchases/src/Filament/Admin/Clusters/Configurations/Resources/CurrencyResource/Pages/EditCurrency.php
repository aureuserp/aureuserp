<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource\Pages;

use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\EditCurrency as BaseEditCurrency;

class EditCurrency extends BaseEditCurrency
{
    protected static string $resource = CurrencyResource::class;
}
