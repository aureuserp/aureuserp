<?php

namespace Webkul\Account\Filament\Resources\CashRounding;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\CashRounding\Pages\CreateCashRounding;
use Webkul\Account\Filament\Resources\CashRounding\Pages\EditCashRounding;
use Webkul\Account\Filament\Resources\CashRounding\Pages\ListCashRounding;
use Webkul\Account\Filament\Resources\CashRounding\Pages\ViewCashRounding;
use Webkul\Account\Filament\Resources\CashRounding\Schemas\CashRoundingForm;
use Webkul\Account\Filament\Resources\CashRounding\Schemas\CashRoundingInfolist;
use Webkul\Account\Filament\Resources\CashRounding\Tables\CashRoundingTable;
use Webkul\Account\Models\CashRounding;

class CashRoundingResource extends Resource
{
    protected static ?string $model = CashRounding::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return CashRoundingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashRoundingTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CashRoundingInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCashRounding::route('/'),
            'create' => CreateCashRounding::route('/create'),
            'view'   => ViewCashRounding::route('/{record}'),
            'edit'   => EditCashRounding::route('/{record}/edit'),
        ];
    }
}
