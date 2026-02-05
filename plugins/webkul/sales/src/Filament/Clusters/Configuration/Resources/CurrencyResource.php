<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources;

use Webkul\Sale\Filament\Clusters\Configuration;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\CurrencyResource\Pages\CreateCurrency;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\CurrencyResource\Pages\EditCurrency;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\CurrencyResource\Pages\ListCurrencies;
use Webkul\Sale\Filament\Clusters\Configuration\Resources\CurrencyResource\Pages\ViewCurrency;
use Webkul\Sale\Models\Currency;
use Webkul\Support\Filament\Resources\CurrencyResource as BaseCurrencyResource;

class CurrencyResource extends BaseCurrencyResource
{
    protected static ?string $model = Currency::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 7;

    protected static ?string $cluster = Configuration::class;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/currency.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/configurations/resources/currency.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('sales::filament/clusters/configurations/resources/currency.navigation.group');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCurrencies::route('/'),
            'create' => CreateCurrency::route('/create'),
            'edit'   => EditCurrency::route('/{record}/edit'),
            'view'   => ViewCurrency::route('/{record}'),
        ];
    }
}
