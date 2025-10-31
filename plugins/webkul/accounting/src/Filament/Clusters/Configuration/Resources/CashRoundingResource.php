<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages\ListCashRoundings;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages\CreateCashRounding;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages\EditCashRounding;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages\ViewCashRounding;
use Webkul\Account\Filament\Resources\CashRoundingResource as BaseCashRoundingResource;

class CashRoundingResource extends BaseCashRoundingResource
{
    protected static bool $shouldRegisterNavigation = true;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/cash-rounding.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/configurations/resources/cash-rounding.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('accounting::filament/clusters/configurations/resources/cash-rounding.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCashRoundings::route('/'),
            'create' => CreateCashRounding::route('/create'),
            'edit' => EditCashRounding::route('/{record}/edit'),
            'view' => ViewCashRounding::route('/{record}'),
        ];
    }
}
