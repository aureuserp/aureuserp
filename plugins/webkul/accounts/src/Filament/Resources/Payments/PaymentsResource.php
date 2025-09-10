<?php

namespace Webkul\Account\Filament\Resources\Payments;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Filament\Resources\Payments\Pages\CreatePayments;
use Webkul\Account\Filament\Resources\Payments\Pages\EditPayments;
use Webkul\Account\Filament\Resources\Payments\Pages\ListPayments;
use Webkul\Account\Filament\Resources\Payments\Pages\ViewPayments;
use Webkul\Account\Filament\Resources\Payments\Schemas\PaymentForm;
use Webkul\Account\Filament\Resources\Payments\Schemas\PaymentInfolist;
use Webkul\Account\Filament\Resources\Payments\Tables\PaymentsTable;
use Webkul\Account\Models\Payment;

class PaymentsResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PaymentInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPayments::route('/'),
            'create' => CreatePayments::route('/create'),
            'view'   => ViewPayments::route('/{record}'),
            'edit'   => EditPayments::route('/{record}/edit'),
        ];
    }
}
