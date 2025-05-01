<?php

namespace Webkul\Account\Filament\Resources\FiscalPositionResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Webkul\Account\Traits\FiscalPositionTax;

class FiscalPositionTaxRelationManager extends RelationManager
{
    use FiscalPositionTax;

    protected static string $relationship = 'fiscalPositionTaxes';

    protected static ?string $title = 'Fiscal Position Taxes';
}
