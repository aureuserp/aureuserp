<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\PackageResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Support\Traits\HasRecordNavigationTabs;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageQuantities extends ManageRelatedRecords
{
    use HasRecordNavigationTabs, HasTableViews;

    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'quantities';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    public static function canAccess(array $parameters = []): bool
    {
        $canAccess = parent::canAccess($parameters);

        if (! $canAccess) {
            return false;
        }

        if (! $parameters['record']->is_storable) {
            return false;
        }

        return app(OperationSettings::class)->enable_packages
            || app(WarehouseSettings::class)->enable_locations
            || (
                app(TraceabilitySettings::class)->enable_lots_serial_numbers
                && $parameters['record']->tracking != ProductTracking::QTY
            );
    }

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/products/resources/product/pages/manage-quantities.title');
    }

    public function getPresetTableViews(): array
    {
        return [
            'internal_locations' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.internal-locations'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-building-office')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('location', function (Builder $query) {
                        $query->where('type', LocationType::INTERNAL);
                    });
                }),
            'transit_locations' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.transit-locations'))
                ->favorite()
                ->icon('heroicon-s-truck')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereHas('location', function (Builder $query) {
                        $query->where('type', LocationType::TRANSIT);
                    });
                }),
            'on_hand' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.on-hand'))
                ->favorite()
                ->icon('heroicon-s-clipboard-document-list')
                ->modifyQueryUsing(function (Builder $query) {
                    $query
                        ->where('quantity', '>', 0)
                        ->whereHas('location', function (Builder $query) {
                            $query->where('is_scrap', false);
                        });
                }),
            'to_count' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.to-count'))
                ->favorite()
                ->icon('heroicon-s-calculator')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('scheduled_at', '>', now())),
            'to_apply' => PresetView::make(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.tabs.to-apply'))
                ->favorite()
                ->icon('heroicon-s-check-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('inventory_quantity_set', true)),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.product'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query) => $query->where('parent_id', $this->getOwnerRecord()->id),
                    )
                    ->searchable()
                    ->preload()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $set('package_id', null);
                    })
                    ->visible((bool) $this->getOwnerRecord()->is_configurable),
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
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        $set('package_id', null);
                    })
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                Select::make('lot_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.lot'))
                    ->relationship(
                        name: 'lot',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query, Get $get) {
                            $productId = $get('product_id') ?? $this->getOwnerRecord()->id;

                            return $query->where('product_id', $productId);
                        },
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => LotResource::form($schema))
                    ->createOptionAction(function (Action $action, Get $get) {
                        $action
                            ->mutateDataUsing(function (array $data) use ($get) {
                                $data['product_id'] = $get('product_id') ?? $this->getOwnerRecord()->id;

                                return $data;
                            });
                    })
                    ->visible(fn (TraceabilitySettings $settings) => $settings->enable_lots_serial_numbers && $this->getOwnerRecord()->tracking != ProductTracking::QTY),
                Select::make('package_id')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.package'))
                    ->relationship(
                        name: 'package',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(function ($query) use ($get) {
                            $locationId = $get('location_id');

                            if ($locationId) {
                                $query->where('location_id', $locationId);
                            } else {
                                $query->whereNull('location_id');
                            }
                        }),
                    )
                    ->getOptionLabelUsing(fn ($record) => $record?->name)
                    ->searchable()
                    ->reactive()
                    ->preload()
                    ->createOptionForm(fn (Schema $schema): Schema => PackageResource::form($schema))
                    ->createOptionAction(function (Action $action, Set $set) {
                        $action
                            ->mutateDataUsing(function (array $data) {
                                $data['company_id'] = $this->getOwnerRecord()->company_id;
                                $data['creator_id'] = filament()->auth()->user()->id;

                                return $data;
                            })
                            ->after(fn () => $set('package_id', null));
                    })
                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),
                TextInput::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.form.fields.on-hand-qty'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(99999999999)
                    ->maxValue(fn () => $this->getOwnerRecord()->tracking == ProductTracking::SERIAL ? 1 : 999999999)
                    ->default(0)
                    ->required(),
            ])
            ->columns(1);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.product'))
                    ->searchable()
                    ->sortable()
                    ->visible((bool) $this->getOwnerRecord()->is_configurable),
                TextColumn::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.location'))
                    ->searchable()
                    ->sortable()
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.storage-category'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.package'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),
                TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.lot'))
                    ->searchable()
                    ->placeholder('—')
                    ->visible(fn (TraceabilitySettings $settings) => $settings->enable_lots_serial_numbers && $this->getOwnerRecord()->tracking != ProductTracking::QTY),
                TextInputColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.on-hand'))
                    ->sortable()
                    ->rules([
                        'numeric',
                        'min:1',
                        'max:'.($this->getOwnerRecord()->tracking == ProductTracking::SERIAL ? '1' : '999999999'),
                    ])
                    ->beforeStateUpdated(function ($record, $state) {
                        $previousQuantity = $record->quantity;

                        if ($previousQuantity == $state) {
                            return;
                        }

                        $adjustmentLocation = Location::where('type', LocationType::INVENTORY)
                            ->where('is_scrap', false)
                            ->first();

                        $currentQuantity = $state - $previousQuantity;

                        if ($currentQuantity < 0) {
                            $sourceLocationId = $record->location_id;

                            $destinationLocationId = $adjustmentLocation->id;
                        } else {
                            $sourceLocationId = $adjustmentLocation->id;

                            $destinationLocationId = $record->location_id;
                        }

                        ProductResource::createMove($record, $currentQuantity, $sourceLocationId, $destinationLocationId);
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        $adjustmentLocation = Location::where('type', LocationType::INVENTORY)
                            ->where('is_scrap', false)
                            ->first();

                        $data['inventory_quantity_set'] = false;

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

                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.on-hand-before-state-updated.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.on-hand-before-state-updated.notification.body'))
                            ->success()
                            ->send();
                    })
                    ->summarize(Sum::make()),
                TextColumn::make('reserved_quantity')
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.columns.reserved-quantity'))
                    ->sortable()
                    ->summarize(Sum::make()),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('inventories::filament/clusters/products/resources/product/pages/manage-quantities.table.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['product_id'] ??= $this->getOwnerRecord()->id;

                        $data['location_id'] = $data['location_id'] ?? Warehouse::first()->lot_stock_location_id;

                        $data['creator_id'] = Auth::id();

                        $data['company_id'] = $this->getOwnerRecord()->company_id;

                        $data['inventory_quantity_set'] = false;

                        $data['counted_quantity'] = 0;

                        $data['incoming_at'] = now();

                        return $data;
                    })
                    ->before(function (array $data) {
                        $productId = $data['product_id'] ?? $this->getOwnerRecord()->id;

                        $existingQuantity = ProductQuantity::where('location_id', $data['location_id'] ?? Warehouse::first()->lot_stock_location_id)
                            ->where('product_id', $productId)
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
                    ->action(function (array $data) {
                        if ($this->getOwnerRecord()->is_configurable) {
                            $record = ProductQuantity::create($data);
                        } else {
                            $data['product_id'] = $this->getOwnerRecord()->id;

                            $record = $this->getOwnerRecord()->quantities()->create($data);
                        }

                        $adjustmentLocation = Location::where('type', LocationType::INVENTORY)
                            ->where('is_scrap', false)
                            ->first();

                        ProductQuantity::updateOrCreate(
                            [
                                'location_id' => $adjustmentLocation->id,
                                'product_id'  => $record->product_id,
                                'lot_id'      => $record->lot_id,
                            ], [
                                'quantity'    => -$record->product->on_hand_quantity,
                                'company_id'  => $record->company_id,
                                'creator_id'  => Auth::id(),
                                'incoming_at' => now(),
                            ]
                        );

                        if ($record->package) {
                            $record->package->update([
                                'location_id' => $record->location_id,
                                'pack_date'   => now(),
                            ]);
                        }

                        if ($record->lot) {
                            $record->lot->update([
                                'location_id' => $record->location_id,
                            ]);
                        }

                        ProductResource::createMove($record, $record->quantity, $adjustmentLocation->id, $record->location_id);

                        return $record;
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
            ])
            ->paginated(false);
    }
}
