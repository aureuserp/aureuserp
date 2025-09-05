<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\CreateQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\EditQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\ListQuotations;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\ManageDeliveries;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\ManageInvoices;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Pages\ViewQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Schemas\QuotationForm;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Schemas\QuotationInfolist;
use Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Tables\QuotationsTable;
use Webkul\Sale\Models\Order;

class QuotationResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = Orders::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/quotation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/quotation.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return QuotationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuotationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuotationInfolist::configure($schema);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewQuotation::class,
            EditQuotation::class,
            ManageInvoices::class,
            ManageDeliveries::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => ListQuotations::route('/'),
            'create'     => CreateQuotation::route('/create'),
            'view'       => ViewQuotation::route('/{record}'),
            'edit'       => EditQuotation::route('/{record}/edit'),
            'invoices'   => ManageInvoices::route('/{record}/invoices'),
            'deliveries' => ManageDeliveries::route('/{record}/deliveries'),
        ];
    }
}
