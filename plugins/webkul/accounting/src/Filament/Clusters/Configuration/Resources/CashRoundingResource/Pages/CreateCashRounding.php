<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages;

use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\CreateCashRounding as BaseCreateCashRounding;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource;

class CreateCashRounding extends BaseCreateCashRounding
{
    protected static string $resource = CashRoundingResource::class;
}
