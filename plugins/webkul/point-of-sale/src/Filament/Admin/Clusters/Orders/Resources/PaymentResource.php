<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PaymentResource\Pages\ListPayments;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\PaymentResource\Tables\PaymentsTable;
use Webkul\PointOfSale\Models\Payment;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 3;

    protected static ?string $cluster = Orders::class;

    protected static ?string $recordTitleAttribute = 'uuid';

    public static function getModelLabel(): string
    {
        return __('point-of-sale::models/payment.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('point-of-sale::models/payment.plural-title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function getNavigationLabel(): string
    {
        return __('point-of-sale::filament/admin/clusters/orders/resources/payment.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
        ];
    }
}
