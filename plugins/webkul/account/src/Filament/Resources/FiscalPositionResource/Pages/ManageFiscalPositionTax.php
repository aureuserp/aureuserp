<?php

namespace Webkul\Account\Filament\Resources\FiscalPositionResource\Pages;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\ManageRelatedRecords;
use Webkul\Account\Filament\Resources\FiscalPositionResource;
use Webkul\Account\Traits\FiscalPositionTax;

class ManageFiscalPositionTax extends ManageRelatedRecords
{
    use FiscalPositionTax;

    protected static string $resource = FiscalPositionResource::class;

    protected static string $relationship = 'fiscalPositionTaxes';

    protected static ?string $navigationIcon = 'heroicon-o-document';

    public function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    public static function getNavigationLabel(): string
    {
        return __('accounts::filament/resources/fiscal-position/pages/manage-fiscal-position.navigation.title');
    }
}
