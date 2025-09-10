<?php

namespace Webkul\Account\Filament\Resources\Taxes;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\Taxes\Pages\CreateTax;
use Webkul\Account\Filament\Resources\Taxes\Pages\EditTax;
use Webkul\Account\Filament\Resources\Taxes\Pages\ListTaxes;
use Webkul\Account\Filament\Resources\Taxes\Pages\ViewTax;
use Webkul\Account\Filament\Resources\Taxes\Schemas\TaxForm;
use Webkul\Account\Filament\Resources\Taxes\Schemas\TaxInfolist;
use Webkul\Account\Filament\Resources\Taxes\Tables\TaxesTable;
use Webkul\Account\Models\Tax;

class TaxResource extends Resource
{
    protected static ?string $model = Tax::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Start;

    public static function form(Schema $schema): Schema
    {
        return TaxForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TaxesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TaxInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTaxes::route('/'),
            'create' => CreateTax::route('/create'),
            'view'   => ViewTax::route('/{record}'),
            'edit'   => EditTax::route('/{record}/edit'),
        ];
    }
}
