<?php

namespace Webkul\Support\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\CreateCurrency;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\EditCurrency;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ListCurrencies;
use Webkul\Support\Filament\Resources\CurrencyResource\Pages\ViewCurrency;
use Webkul\Support\Filament\Resources\CurrencyResource\Schemas\CurrencyForm;
use Webkul\Support\Filament\Resources\CurrencyResource\Schemas\CurrencyInfolist;
use Webkul\Support\Filament\Resources\CurrencyResource\Tables\CurrenciesTable;
use Webkul\Support\Models\Currency;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('support::filament/resources/currency.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function form(Schema $schema): Schema
    {
        return CurrencyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CurrenciesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CurrencyInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCurrencies::route('/'),
            'create' => CreateCurrency::route('/create'),
            'view'   => ViewCurrency::route('/{record}'),
            'edit'   => EditCurrency::route('/{record}/edit'),
        ];
    }
}
