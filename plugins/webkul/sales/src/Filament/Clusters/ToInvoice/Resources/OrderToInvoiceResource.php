<?php

namespace Webkul\Sale\Filament\Clusters\ToInvoice\Resources;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Filament\Clusters\ToInvoice;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoiceResource\Pages\EditOrderToInvoice;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoiceResource\Pages\ListOrderToInvoices;
use Webkul\Sale\Filament\Clusters\ToInvoice\Resources\OrderToInvoiceResource\Pages\ViewOrderToInvoice;
use Webkul\Sale\Models\Order;

class OrderToInvoiceResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-arrow-down';

    protected static ?string $cluster = ToInvoice::class;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/to-invoice/resources/order-to-invoice.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/to-invoice/resources/order-to-invoice.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return QuotationResource::form($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationResource::table($table)
            ->modifyQueryUsing(function ($query) {
                $query->where('invoice_status', InvoiceStatus::TO_INVOICE);
            });
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuotationResource::infolist($schema);
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
