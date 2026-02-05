<?php

namespace Webkul\Accounting\Filament\Clusters\Configuration\Resources;

use Webkul\Account\Filament\Resources\CashRoundingResource as BaseCashRoundingResource;
use Webkul\Accounting\Filament\Clusters\Configuration;
use Webkul\Accounting\Filament\Clusters\Configuration\Resources\CashRoundingResource\Pages\ListCashRoundings;
use Webkul\Accounting\Models\CashRounding;

class CashRoundingResource extends BaseCashRoundingResource
{
    protected static ?string $model = CashRounding::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 9;

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
        ];
    }
}
