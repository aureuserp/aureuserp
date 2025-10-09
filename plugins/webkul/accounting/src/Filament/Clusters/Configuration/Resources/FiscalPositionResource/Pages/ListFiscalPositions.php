<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ListFiscalPositions as BaseListFiscalPositions;

class ListFiscalPositions extends BaseListFiscalPositions
{
    protected static string $resource = FiscalPositionResource::class;
}
