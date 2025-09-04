<?php

namespace Webkul\Sale\Filament\Clusters\ToInvoice\Resources;

use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\ToInvoice;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoiceResource\Pages;

class OrderToInvoiceResource extends QuotationResource
{
    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $cluster = ToInvoice::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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

        return $query->where('invoice_status', InvoiceStatus::TO_INVOICE);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewOrderToInvoice::class,
            Pages\EditOrderToInvoice::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrderToInvoices::route('/'),
            'view'  => Pages\ViewOrderToInvoice::route('/{record}'),
            'edit'  => Pages\EditOrderToInvoice::route('/{record}/edit'),
        ];
    }
}
