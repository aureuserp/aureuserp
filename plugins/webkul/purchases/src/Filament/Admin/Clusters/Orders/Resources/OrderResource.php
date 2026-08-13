<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas\OrderForm;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas\OrderInfolist;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Tables\OrdersTable;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Settings\OrderSettings;

class OrderResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Order::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'partner.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('purchases::filament/admin/clusters/orders/resources/order.global-search.vendor') => $record->partner?->name ?? '—',
            __('purchases::filament/admin/clusters/orders/resources/order.global-search.amount') => money($record->total_amount ?? 0, $record->currency?->name) ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return OrderForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return OrdersTable::configure(
            $table,
            static::class,
            static::getCustomTableColumns(),
            static::getTableQueryBuilderConstraints(),
        );
    }

    public static function infolist(Schema $schema): Schema
    {
        return OrderInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getOrderSettings(): OrderSettings
    {
        return settings(OrderSettings::class);
    }

    public static function getProductSettings(): ProductSettings
    {
        return settings(ProductSettings::class);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('id');
    }
}
