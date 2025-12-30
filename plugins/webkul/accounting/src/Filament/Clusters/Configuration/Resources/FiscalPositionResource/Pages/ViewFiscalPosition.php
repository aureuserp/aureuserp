<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource\Pages;

use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\ViewFiscalPosition as BaseViewFiscalPosition;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource;

class ViewFiscalPosition extends BaseViewFiscalPosition
{
    protected static string $resource = FiscalPositionResource::class;
}
