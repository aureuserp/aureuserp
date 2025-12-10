<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages;

use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\EditCashRounding as BaseEditCashRounding;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource;

class EditCashRounding extends BaseEditCashRounding
{
    protected static string $resource = CashRoundingResource::class;
}
