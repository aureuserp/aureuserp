<?php

namespace Webkul\Manufacturing\Filament\Clusters\Operations\Resources;

use BackedEnum;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Inventory\Models\OperationType;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Manufacturing\Filament\Clusters\Operations;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\CreateManufacturingOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\EditManufacturingOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\ListManufacturingOrders;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\ManageTransfers;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\OverviewManufacturingOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Pages\ViewManufacturingOrder;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Schemas\ManufacturingOrderForm;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Schemas\ManufacturingOrderInfolist;
use Webkul\Manufacturing\Filament\Clusters\Operations\Resources\ManufacturingOrderResource\Tables\ManufacturingOrdersTable;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Order;
use Webkul\Manufacturing\Settings\OperationSettings;

class ManufacturingOrderResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Order::class;

    protected static ?string $cluster = Operations::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static function getModelLabel(): string
    {
        return __('manufacturing::models/order.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('manufacturing::filament/clusters/operations/resources/manufacturing-order.navigation.group');
    }

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Start;
    }

    public static function form(Schema $schema): Schema
    {
        return ManufacturingOrderForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return ManufacturingOrdersTable::configure($table, static::getCustomTableColumns());
    }

    public static function infolist(Schema $schema): Schema
    {
        return ManufacturingOrderInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewManufacturingOrder::class,
            EditManufacturingOrder::class,
            OverviewManufacturingOrder::class,
            ManageTransfers::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'     => ListManufacturingOrders::route('/'),
            'create'    => CreateManufacturingOrder::route('/create'),
            'view'      => ViewManufacturingOrder::route('/{record}'),
            'edit'      => EditManufacturingOrder::route('/{record}/edit'),
            'overview'  => OverviewManufacturingOrder::route('/{record}/overview'),
            'transfers' => ManageTransfers::route('/{record}/transfers'),
        ];
    }

    public static function getBillOfMaterialLabel(?BillOfMaterial $billOfMaterial): string
    {
        if (! $billOfMaterial) {
            return '—';
        }

        $reference = $billOfMaterial->code ?: (string) $billOfMaterial->id;
        $productName = $billOfMaterial->product?->name;

        if (! $productName) {
            return $reference;
        }

        return $reference.': '.$productName;
    }

    public static function getOperationTypeLabel(?OperationType $operationType): string
    {
        if (! $operationType) {
            return '—';
        }

        if (! $operationType->warehouse) {
            return $operationType->name;
        }

        return $operationType->warehouse->name.': '.$operationType->name;
    }

    public static function getWarehouseSettings(): WarehouseSettings
    {
        return settings(WarehouseSettings::class);
    }

    public static function getOperationSettings(): OperationSettings
    {
        return settings(OperationSettings::class);
    }
}
