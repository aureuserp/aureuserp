<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource\Pages;

use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource;
use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\CreateFiscalPosition as BaseCreateFiscalPosition;

class CreateFiscalPosition extends BaseCreateFiscalPosition
{
    protected static string $resource = FiscalPositionResource::class;
}
