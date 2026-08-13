<?php

namespace Webkul\Inventory\Filament\Clusters\Reporting\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Reporting;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource\Pages\ManageQuantities;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource\Schemas\QuantityForm;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource\Tables\QuantitiesTable;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Settings\ProductSettings;

class QuantityResource extends Resource
{
    protected static ?string $model = ProductQuantity::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?int $navigationSort = 1;

    protected static ?string $cluster = Reporting::class;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/reporting.quantities.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return QuantityForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QuantitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageQuantities::route('/'),
        ];
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
}
