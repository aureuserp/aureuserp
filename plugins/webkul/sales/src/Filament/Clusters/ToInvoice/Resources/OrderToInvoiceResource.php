<?php

namespace Webkul\Sale\Filament\Clusters\ToInvoice\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\ToInvoice;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoiceResource\Pages\EditOrderToInvoice;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoiceResource\Pages\ListOrderToInvoices;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoiceResource\Pages\ViewOrderToInvoice;
use Webkul\Sale\Models\OrderToInvoice as Order;

class OrderToInvoiceResource extends QuotationResource
{
    protected static ?string $model = Order::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $cluster = ToInvoice::class;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/to-invoice/resources/order-to-invoice.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/to-invoice/resources/order-to-invoice.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        $query = static::getModel()::applyPermissionScope($query);

        return $query
            ->where('invoice_status', InvoiceStatus::TO_INVOICE)
            ->orderByDesc('id');
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewOrderToInvoice::class,
            EditOrderToInvoice::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOrderToInvoices::route('/'),
            'view'  => ViewOrderToInvoice::route('/{record}'),
            'edit'  => EditOrderToInvoice::route('/{record}/edit'),
        ];
    }
}
