<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages;

use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\ViewCashRounding as BaseViewCashRounding;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource;

class ViewCashRounding extends BaseViewCashRounding
{
    protected static string $resource = CashRoundingResource::class;
}
