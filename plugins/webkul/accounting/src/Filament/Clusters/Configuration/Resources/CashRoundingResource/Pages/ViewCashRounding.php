<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource;
use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\ViewCashRounding as BaseViewCashRounding;

class ViewCashRounding extends BaseViewCashRounding
{
    protected static string $resource = CashRoundingResource::class;
}
