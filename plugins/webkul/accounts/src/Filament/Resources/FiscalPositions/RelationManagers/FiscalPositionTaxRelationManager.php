<?php

namespace Webkul\Account\Filament\Resources\FiscalPositions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Webkul\Account\Traits\FiscalPositionTax;

class FiscalPositionTaxRelationManager extends RelationManager
{
    use FiscalPositionTax;

    protected static string $relationship = 'fiscalPositionTaxes';

    protected static ?string $title = 'Fiscal Position Taxes';
}
