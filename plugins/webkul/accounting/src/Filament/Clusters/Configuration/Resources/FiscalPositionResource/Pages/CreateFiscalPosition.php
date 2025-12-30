<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource\Pages;

use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\CreateFiscalPosition as BaseCreateFiscalPosition;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource;

class CreateFiscalPosition extends BaseCreateFiscalPosition
{
    protected static string $resource = FiscalPositionResource::class;
}
