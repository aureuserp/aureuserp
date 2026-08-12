<?php

namespace Webkul\Inventory\Filament\Clusters\Reporting\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Table;
use Webkul\Inventory\Filament\Clusters\Reporting;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\MoveResource\Pages\ManageMoves;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\MoveResource\Tables\MovesTable;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Settings\ProductSettings;

class MoveResource extends Resource
{
    protected static ?string $model = MoveLine::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = Reporting::class;

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/reporting.moves.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return MovesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMoves::route('/'),
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
