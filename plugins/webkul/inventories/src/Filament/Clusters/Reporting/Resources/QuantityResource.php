<?php

namespace Webkul\Inventory\Filament\Clusters\Reporting\Resources;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Filament\Clusters\Reporting;
use Webkul\Inventory\Filament\Clusters\Reporting\Resources\QuantityResource\Pages\ManageQuantities;
use Webkul\Inventory\Models\PackageType;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Product\Models\Category;
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
        return $schema
            ->components([
                Select::make('product_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.product'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('is_storable', true)->whereNull('is_configurable'),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('lot_id', null);
                        $set('package_id', null);
                    }),
                Select::make('location_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.location'))
                    ->relationship(
                        name: 'location',
                        titleAttribute: 'full_name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', LocationType::INTERNAL),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set) {
                        $set('package_id', null);
                    })
                    ->visible(static::getWarehouseSettings()->enable_locations),
                Select::make('lot_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.lot'))
                    ->relationship(
                        name: 'lot',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => LotResource::form($schema))
                    ->createOptionAction(function (Action $action, Get $get) {
                        $action->mutateDataUsing(function (array $data) use ($get) {
                            $data['product_id'] = $get('product_id');

                            return $data;
                        });
                    })
                    ->visible(fn (TraceabilitySettings $settings) => $settings->enable_lots_serial_numbers),
                Select::make('package_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.package'))
                    ->relationship(
                        name: 'package',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->where('location_id', $get('location_id'))
                            ->orWhereNull('location_id'),
                    )
                    ->searchable()
                    ->reactive()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => PackageResource::form($schema))
                    ->createOptionUsing(function (array $data, Get $get) {
                        $data['location_id'] = $get('location_id');

                        return PackageResource::getModel()::create($data)->getKey();
                    })
                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),
                TextInput::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.on-hand-qty'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required(),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->groups([
                Group::make('product.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.product')),
                Group::make('product.category.full_name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.product-category')),
                Group::make('location.full_name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.location')),
                Group::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.storage-category')),
                Group::make('lot.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.lot')),
                Group::make('package.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.package')),
                Group::make('company.name')
                    ->label(__('inventories::filament/clusters/reporting.quantities.groups.company')),
            ])
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.location'))
                    ->searchable()
                    ->sortable()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.storage-category'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.package'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getOperationSettings()->enable_packages),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.lot'))
                    ->searchable()
                    ->placeholder('—')
                    ->visible(static::getTraceabilitySettings()->enable_lots_serial_numbers),
                TextInputColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.on-hand'))
                    ->sortable()
                    ->rules(['numeric', 'min:1', 'max:999999999'])
                    ->beforeStateUpdated(function ($record, $state) {
                        $previousQuantity = $record->quantity;

                        if ($previousQuantity == $state) {
                            return;
                        }

                        $record->update([
                            'quantity'                => $state,
                            'inventory_diff_quantity' => $state - $previousQuantity,
                        ]);
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.on-hand-before-state-updated.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.on-hand-before-state-updated.notification.body'))
                            ->send();
                    })
                    ->summarize(Sum::make()),
                TextColumn::make('reserved_quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.reserved-quantity'))
                    ->sortable()
                    ->summarize(Sum::make()),
                TextColumn::make('product.uom.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.unit'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getProductSettings()->enable_uom),
            ])
            ->filters([
                SelectFilter::make('warehouse')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.warehouse'))
                    ->options(fn () => Warehouse::query()->orderBy('name')->pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getWarehouseSettings()->enable_locations)
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('location', fn (Builder $q) => $q->whereIn('warehouse_id', $data['values']))),
                SelectFilter::make('location')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.location'))
                    ->relationship('location', 'full_name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                SelectFilter::make('product_category')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.product-category'))
                    ->options(fn () => Category::get()->pluck('full_name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('product', fn (Builder $q) => $q->whereIn('category_id', $data['values']))),
                SelectFilter::make('storageCategory')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.storage-category'))
                    ->relationship('storageCategory', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                SelectFilter::make('package')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.package'))
                    ->relationship('package', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getOperationSettings()->enable_packages),
                SelectFilter::make('package_type')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.package-type'))
                    ->options(fn () => PackageType::query()->orderBy('name')->pluck('name', 'id'))
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getOperationSettings()->enable_packages)
                    ->query(fn (Builder $query, array $data) => empty($data['values'])
                        ? $query
                        : $query->whereHas('package', fn (Builder $q) => $q->whereIn('package_type_id', $data['values']))),
                SelectFilter::make('lot')
                    ->label(__('inventories::filament/clusters/reporting.quantities.filters.lot'))
                    ->relationship('lot', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->visible(static::getTraceabilitySettings()->enable_lots_serial_numbers),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['location_id'] = $data['location_id'] ?? Warehouse::first()->lot_stock_location_id;

                        $data['company_id'] = Product::find($data['product_id'])?->company_id;

                        $data['inventory_diff_quantity'] = $data['quantity'];

                        return $data;
                    })
                    ->before(function (array $data) {
                        $existingQuantity = ProductQuantity::where('location_id', $data['location_id'] ?? Warehouse::first()->lot_stock_location_id)
                            ->where('product_id', $data['product_id'])
                            ->where('package_id', $data['package_id'] ?? null)
                            ->where('lot_id', $data['lot_id'] ?? null)
                            ->exists();

                        if ($existingQuantity) {
                            Notification::make()
                                ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.before.notification.title'))
                                ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.before.notification.body'))
                                ->warning()
                                ->send();

                            $this->halt();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.actions.delete.notification.body')),
                    ),
            ]);
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
