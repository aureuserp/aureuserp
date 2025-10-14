<?php

namespace Webkul\Sale\Filament\Clusters\ToInvoice\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\ToInvoice;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToUpsellResource\Pages\ListOrderToUpsells;
use Webkul\Sale\Models\Order;

class OrderToUpsellResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $cluster = ToInvoice::class;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/to-invoice/resources/order-to-upsell.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/to-invoice/resources/order-to-upsell.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return QuotationResource::form($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuotationResource::infolist($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationResource::table($table)
            ->modifyQueryUsing(function ($query) {
                $query->where('invoice_status', InvoiceStatus::UP_SELLING);
            });
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOrderToUpsells::route('/'),
        ];
    }
}
