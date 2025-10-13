<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Operations;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\QuantityResource\Pages\ManageQuantities;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\ProductSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;

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
        return $schema
            ->components([
                Select::make('location_id')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.location'))
                    ->relationship(
                        name: 'location',
                        titleAttribute: 'full_name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('type', LocationType::INTERNAL),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                Select::make('product_id')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.product'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('is_storable', true)->whereNull('is_configurable'),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $set('lot_id', null);
                    }),
                Select::make('lot_id')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.lot'))
                    ->relationship(
                        name: 'lot',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                    )
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => LotResource::form($schema))
                    ->createOptionAction(function (Action $action, Get $get) {
                        $action
                            ->mutateDataUsing(function (array $data) use ($get): array {
                                $data['product_id'] = $get('product_id');

                                return $data;
                            });
                    })
                    ->visible(function (Get $get): bool {
                        if (! static::getTraceabilitySettings()->enable_lots_serial_numbers) {
                            return false;
                        }

                        $product = Product::find($get('product_id'));

                        if (! $product) {
                            return false;
                        }

                        return $product->tracking === ProductTracking::LOT;
                    }),
                Select::make('package_id')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.package'))
                    ->relationship('package', 'name')
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => PackageResource::form($schema))
                    ->visible(static::getOperationSettings()->enable_packages),
                TextInput::make('counted_quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.counted-qty'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->required(),
                DatePicker::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.form.fields.scheduled-at'))
                    ->native(false)
                    ->default(now()->setDay(static::getOperationSettings()->annual_inventory_day)->setMonth(static::getOperationSettings()->annual_inventory_month)),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columnManagerColumns(2)
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('location.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.location'))
                    ->searchable()
                    ->sortable()
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.storage-category'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('product.category.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.product-category'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.lot'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getTraceabilitySettings()->enable_lots_serial_numbers),
                TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.package'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(static::getOperationSettings()->enable_packages),
                TextColumn::make('available_quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.available-quantity'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.on-hand'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextInputColumn::make('counted_quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.counted'))
                    ->sortable()
                    ->rules(['numeric', 'min:0'])
                    ->beforeStateUpdated(function ($record, $state) {
                        $record->update([
                            'inventory_quantity_set'  => true,
                            'inventory_diff_quantity' => $state - $record->quantity,
                        ]);
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/quantity.table.columns.on-hand-before-state-updated.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/quantity.table.columns.on-hand-before-state-updated.notification.body'))
                            ->success()
                            ->send();
                    }),
                TextColumn::make('inventory_diff_quantity')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.difference'))
                    ->sortable()
                    ->formatStateUsing(fn ($record) => $record->inventory_quantity_set ? $record->inventory_diff_quantity : '')
                    ->color(fn ($record) => $record->inventory_diff_quantity > 0 ? 'success' : 'danger'),
                TextColumn::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.scheduled-at'))
                    ->sortable()
                    ->date()
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('user.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.user'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('company.name')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.columns.company'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups(
                collect([
                    Group::make('product.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.product')),
                    Group::make('product.category.full_name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.product-category')),
                    Group::make('location.full_name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.location')),
                    Group::make('storageCategory.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.storage-category')),
                    Group::make('lot.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.lot')),
                    Group::make('package.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.package')),
                    Group::make('company.name')
                        ->label(__('inventories::filament/clusters/operations/resources/quantity.table.groups.company')),
                ])->filter(function ($group) {
                    return match ($group->getId()) {
                        'location.full_name', 'storageCategory.name' => static::getWarehouseSettings()->enable_locations,
                        'lot.name'     => static::getTraceabilitySettings()->enable_lots_serial_numbers,
                        'package.name' => static::getOperationSettings()->enable_packages,
                        default        => true
                    };
                })->all()
            )
            ->filters([
                QueryBuilder::make()
                    ->constraints(collect([
                        static::getWarehouseSettings()->enable_locations
                            ? RelationshipConstraint::make('location')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.location'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('full_name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-map-pin')
                            : null,
                        static::getWarehouseSettings()->enable_locations
                            ? RelationshipConstraint::make('storageCategory')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.storage-category'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('full_name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-folder')
                                : null,
                        RelationshipConstraint::make('product')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.product'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-shopping-bag'),
                        static::getProductSettings()->enable_uom
                            ? RelationshipConstraint::make('uom')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.uom'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-shopping-bag')
                            : null,
                        RelationshipConstraint::make('product.category')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.product-category'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('full_name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-folder'),
                        static::getTraceabilitySettings()->enable_lots_serial_numbers
                            ? RelationshipConstraint::make('lot')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.lot'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-rectangle-stack')
                            : null,
                        static::getOperationSettings()->enable_packages
                            ? RelationshipConstraint::make('package')
                                ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.package'))
                                ->multiple()
                                ->selectable(
                                    IsRelatedToOperator::make()
                                        ->titleAttribute('name')
                                        ->searchable()
                                        ->multiple()
                                        ->preload(),
                                )
                                ->icon('heroicon-o-cube')
                            : null,
                        NumberConstraint::make('quantity')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.on-hand-quantity'))
                            ->icon('heroicon-o-scale'),
                        NumberConstraint::make('difference')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.difference-quantity'))
                            ->icon('heroicon-o-scale'),
                        RelationshipConstraint::make('user')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.user'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                        DateConstraint::make('incoming_at')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.incoming-at')),
                        DateConstraint::make('scheduled_at')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.scheduled-at')),
                        DateConstraint::make('created_at')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.updated-at')),
                        RelationshipConstraint::make('company')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-building-office'),
                        RelationshipConstraint::make('creator')
                            ->label(__('inventories::filament/clusters/operations/resources/quantity.table.filters.creator'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            )
                            ->icon('heroicon-o-user'),
                    ])->filter()->values()->all()),
            ], layout: FiltersLayout::Modal)
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->slideOver(),
            )
            ->filtersFormColumns(2)
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $product = Product::find($data['product_id']);

                        $data['location_id'] = $data['location_id'] ?? Warehouse::first()->lot_stock_location_id;

                        $data['creator_id'] = Auth::id();

                        $data['company_id'] = $product->company_id;

                        $data['inventory_quantity_set'] = true;

                        $data['inventory_diff_quantity'] = $data['counted_quantity'];

                        $data['incoming_at'] = now();

                        $data['scheduled_at'] = now()->setDay(static::getOperationSettings()->annual_inventory_day)->setMonth(static::getOperationSettings()->annual_inventory_month);

                        return $data;
                    })
                    ->before(function (CreateAction $action, array $data) {
                        $existingQuantity = ProductQuantity::where('location_id', $data['location_id'] ?? Warehouse::first()->lot_stock_location_id)
                            ->where('product_id', $data['product_id'])
                            ->where('package_id', $data['package_id'] ?? null)
                            ->where('lot_id', $data['lot_id'] ?? null)
                            ->exists();

                        if ($existingQuantity) {
                            Notification::make()
                                ->title(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.before.notification.title'))
                                ->body(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.before.notification.body'))
                                ->warning()
                                ->send();

                            $action->halt();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/quantity.table.header-actions.create.notification.body')),
                    ),
            ])
            ->recordActions([
                Action::make('apply')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.actions.apply.label'))
                    ->icon('heroicon-o-check')
                    ->visible(fn (ProductQuantity $record) => $record->inventory_quantity_set)
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/quantity.table.actions.apply.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/quantity.table.actions.apply.notification.body')),
                    )
                    ->action(function (ProductQuantity $record) {
                        $adjustmentLocation = Location::where('type', LocationType::INVENTORY)
                            ->where('is_scrap', false)
                            ->first();

                        $countedQuantity = $record->counted_quantity;

                        $diffQuantity = $record->inventory_diff_quantity;

                        $record->update([
                            'quantity'                => $countedQuantity,
                            'counted_quantity'        => 0,
                            'inventory_diff_quantity' => 0,
                            'inventory_quantity_set'  => false,
                        ]);

                        ProductQuantity::updateOrCreate(
                            [
                                'location_id' => $adjustmentLocation->id,
                                'product_id'  => $record->product_id,
                                'lot_id'      => $record->lot_id,
                            ], [
                                'quantity'               => -$record->product->on_hand_quantity,
                                'company_id'             => $record->company_id,
                                'creator_id'             => Auth::id(),
                                'incoming_at'            => now(),
                                'inventory_quantity_set' => false,
                            ]
                        );

                        if ($diffQuantity < 0) {
                            $sourceLocationId = $record->location_id;

                            $destinationLocationId = $adjustmentLocation->id;
                        } else {
                            $sourceLocationId = $adjustmentLocation->id;

                            $destinationLocationId = $record->location_id;
                        }

                        ProductResource::createMove($record, abs($diffQuantity), $sourceLocationId, $destinationLocationId);
                    }),
                Action::make('clear')
                    ->label(__('inventories::filament/clusters/operations/resources/quantity.table.actions.clear.label'))
                    ->icon('heroicon-o-x-mark')
                    ->visible(fn (ProductQuantity $record) => $record->inventory_quantity_set)
                    ->color('gray')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/quantity.table.actions.clear.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/quantity.table.actions.clear.notification.body')),
                    )
                    ->action(function (ProductQuantity $record) {
                        $record->update([
                            'inventory_quantity_set'  => false,
                            'counted_quantity'        => 0,
                            'inventory_diff_quantity' => 0,
                        ]);
                    }),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('location', function (Builder $query) {
                    $query->whereIn('type', [LocationType::INTERNAL, LocationType::TRANSIT]);
                });

                $query->whereHas('product', function (Builder $query) {
                    $query->whereNull('deleted_at');
                });
            });
    }

    public static function getOperationSettings(): OperationSettings
    {
        return once(fn () => app(OperationSettings::class));
    }

    public static function getProductSettings(): ProductSettings
    {
        return once(fn () => app(ProductSettings::class));
    }

    public static function getTraceabilitySettings(): TraceabilitySettings
    {
        return once(fn () => app(TraceabilitySettings::class));
    }

    public static function getWarehouseSettings(): WarehouseSettings
    {
        return once(fn () => app(WarehouseSettings::class));
    }

    public static function getPages(): array
    {
        return [
            'index'  => ManageQuantities::route('/'),
        ];
    }
}
