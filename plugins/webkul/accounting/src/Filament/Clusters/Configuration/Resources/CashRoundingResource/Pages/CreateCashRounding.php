<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource;
use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\CreateCashRounding as BaseCreateCashRounding;

class CreateCashRounding extends BaseCreateCashRounding
{
    protected static string $resource = CashRoundingResource::class;
}
