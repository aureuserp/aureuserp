<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources;

use Webkul\Purchase\Filament\Admin\Clusters\Configurations;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource\Pages\CreateCurrency;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource\Pages\EditCurrency;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource\Pages\ListCurrencies;
use Webkul\Purchase\Filament\Admin\Clusters\Configurations\Resources\CurrencyResource\Pages\ViewCurrency;
use Webkul\Purchase\Models\Currency;
use Webkul\Support\Filament\Resources\CurrencyResource as BaseCurrencyResource;

class CurrencyResource extends BaseCurrencyResource
{
    protected static ?string $model = Currency::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 7;

    protected static ?string $cluster = Configurations::class;

    public static function getModelLabel(): string
    {
        return __('purchases::filament/admin/clusters/configurations/resources/currency.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/configurations/resources/currency.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('purchases::filament/admin/clusters/configurations/resources/currency.navigation.group');
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
