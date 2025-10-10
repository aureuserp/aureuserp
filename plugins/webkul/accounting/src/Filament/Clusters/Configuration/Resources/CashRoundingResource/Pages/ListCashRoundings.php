<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource;
use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\ListCashRoundings as BaseListCashRoundings;

class ListCashRoundings extends BaseListCashRoundings
{
    protected static string $resource = CashRoundingResource::class;
}
