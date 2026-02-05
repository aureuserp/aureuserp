<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource\Pages;

use Webkul\Account\Filament\Resources\FiscalPositionResource\Pages\EditFiscalPosition as BaseEditFiscalPosition;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\FiscalPositionResource;

class EditFiscalPosition extends BaseEditFiscalPosition
{
    protected static string $resource = FiscalPositionResource::class;
}
