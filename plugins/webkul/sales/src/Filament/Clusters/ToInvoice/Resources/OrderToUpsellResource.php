<?php

namespace Webkul\Sale\Filament\Clusters\ToInvoice\Resources;

use BackedEnum;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\ToInvoice;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToUpsellResource\Pages\ListOrderToUpsells;
use Webkul\Sale\Models\OrderToUpsell as Order;

class OrderToUpsellResource extends QuotationResource
{
    protected static ?string $cluster = ToInvoice::class;

    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-arrow-up';

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/to-invoice/resources/order-to-upsell.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/to-invoice/resources/order-to-upsell.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $query = static::getModel()::applyPermissionScope($query);

        return $query->where('invoice_status', InvoiceStatus::UP_SELLING);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOrderToUpsells::route('/'),
        ];
    }
}
