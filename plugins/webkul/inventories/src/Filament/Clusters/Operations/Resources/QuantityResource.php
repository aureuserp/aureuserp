<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource\Pages\ManageQuantities;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource\Schemas\QuantityForm;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource\Tables\QuantitiesTable;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Settings\ProductSettings;

class QuantityResource extends Resource
{
    protected static ?string $model = ProductQuantity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-up-down';

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Operations::class;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/quantity.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('inventories::filament/clusters/operations/resources/quantity.navigation.group');
    }

    public static function form(Schema $schema): Schema
    {
        return QuantityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuantitiesTable::configure($table);
    }

    public static function getOperationSettings(): OperationSettings
    {
        return settings(OperationSettings::class);
    }

    public static function getProductSettings(): ProductSettings
    {
        return settings(ProductSettings::class);
    }

    public static function getTraceabilitySettings(): TraceabilitySettings
    {
        return settings(TraceabilitySettings::class);
    }

    public static function getWarehouseSettings(): WarehouseSettings
    {
        return settings(WarehouseSettings::class);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ManageQuantities::route('/'),
        ];
    }
}
