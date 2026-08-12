<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\CreateQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\EditQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ListQuotations;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ManageDeliveries;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ManageInvoices;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Pages\ViewQuotation;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Schemas\QuotationForm;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Schemas\QuotationInfolist;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Tables\QuotationsTable;
use Webkul\Sale\Models\Quotation as Order;

class QuotationResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $cluster = Orders::class;

    public static function getModelLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/quotation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/orders/resources/quotation.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name', 'client_order_ref'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('sales::filament/clusters/orders/resources/quotation.global-search.customer')  => $record->partner?->name ?? '—',
            __('sales::filament/clusters/orders/resources/quotation.global-search.reference') => $record->client_order_ref ?? '—',
            __('sales::filament/clusters/orders/resources/quotation.global-search.amount')    => $record->amount_total ? money($record->amount_total) : '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return QuotationForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return QuotationsTable::configure(
            $table,
            static::class,
            static::getCustomTableColumns(),
            static::getTableQueryBuilderConstraints(),
        );
    }

    public static function infolist(Schema $schema): Schema
    {
        return QuotationInfolist::configure($schema, static::getCustomInfolistEntries());
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
            'operations' => ManageDeliveries::route('/{record}/deliveries'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
