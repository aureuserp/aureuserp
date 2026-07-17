<?php

namespace Webkul\Inventory\Filament\Clusters\Reporting\Resources;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Reporting;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\MoveResource\Pages\ManageMoves;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Models\PackageType;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Models\Category;
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
        return $table
            ->groups([
                Group::make('product.name')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.product')),
                Group::make('state')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.status')),
                Group::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.date'))
                    ->date(),
                Group::make('operation.name')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.operation')),
                Group::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.location')),
                Group::make('product.category.full_name')
                    ->label(__('inventories::filament/clusters/reporting.moves.groups.category')),
            ])
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.product'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.date'))
                    ->sortable()
                    ->dateTime(),
                TextColumn::make('reference')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.lot'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getTraceabilitySettings()->enable_lots_serial_numbers),
                TextColumn::make('resultPackage.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.package'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getOperationSettings()->enable_packages),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.source-location'))
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.destination-location'))
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('uom_qty')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.quantity'))
                    ->sortable()
                    ->color(fn ($record) => $record->destinationLocation->type == LocationType::INTERNAL ? 'success' : 'danger'),
                TextColumn::make('uom.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.unit'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getProductSettings()->enable_uom),
                TextColumn::make('state')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.state'))
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.columns.done-by'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('product_category')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.product-category'))
                    ->options(fn () => Category::get()->pluck('full_name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('product', fn (Builder $q) => $q->whereIn('category_id', $data['values']))),
                SelectFilter::make('sourceLocation')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.source-location'))
                    ->relationship('sourceLocation', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                SelectFilter::make('destinationLocation')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.destination-location'))
                    ->relationship('destinationLocation', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                SelectFilter::make('resultPackage')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.package'))
                    ->relationship('resultPackage', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getOperationSettings()->enable_packages),
                SelectFilter::make('package_type')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.package-type'))
                    ->options(fn () => PackageType::query()->orderBy('name')->pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getOperationSettings()->enable_packages)
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('resultPackage', fn (Builder $q) => $q->whereIn('package_type_id', $data['values']))),
                SelectFilter::make('lot')
                    ->label(__('inventories::filament/clusters/reporting.moves.filters.lot'))
                    ->relationship('lot', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getTraceabilitySettings()->enable_lots_serial_numbers),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-moves.table.actions.delete.notification.body')),
                    ),
            ])
            ->defaultSort('created_at', 'desc');
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
