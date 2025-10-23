<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource;
use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\EditCashRounding as BaseEditCashRounding;

class EditCashRounding extends BaseEditCashRounding
{
    protected static string $resource = CashRoundingResource::class;
}
