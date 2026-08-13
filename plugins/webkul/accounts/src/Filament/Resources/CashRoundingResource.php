<?php

namespace Webkul\Account\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\CashRoundingResource\Pages\ListCashRoundings;
use Webkul\Account\Filament\Resources\CashRoundingResource\Schemas\CashRoundingForm;
use Webkul\Account\Filament\Resources\CashRoundingResource\Schemas\CashRoundingInfolist;
use Webkul\Account\Filament\Resources\CashRoundingResource\Tables\CashRoundingsTable;
use Webkul\Account\Models\CashRounding;
use Webkul\Account\Settings\CustomerInvoiceSettings;

class CashRoundingResource extends Resource
{
    protected static ?string $model = CashRounding::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static bool $shouldRegisterNavigation = false;

    public static function isDiscovered(): bool
    {
        if (app()->runningInConsole()) {
            return true;
        }

        return settings(CustomerInvoiceSettings::class)->group_cash_rounding;
    }

    public static function form(Schema $schema): Schema
    {
        return CashRoundingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CashRoundingsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CashRoundingInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCashRoundings::route('/'),
        ];
    }
}
