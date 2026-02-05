<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ManageQuantities extends ManageRelatedRecords
{
    use HasRecordNavigationTabs;

    protected static string $resource = LotResource::class;

    protected static string $relationship = 'quantities';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    /**
     * @param  array<string, mixed>  $parameters
     */
    public static function canAccess(array $parameters = []): bool
    {
        $canAccess = parent::canAccess($parameters);

        if (! $canAccess) {
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
        return __('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.columns.product'))
                    ->searchable(),
                TextColumn::make('location.full_name')
                    ->label(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.columns.location'))
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.columns.storage-category'))
                    ->placeholder('—'),
                TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.columns.package'))
                    ->placeholder('—')
                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),
                TextInputColumn::make('quantity')
                    ->label(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.columns.on-hand'))
                    ->searchable()
                    ->sortable()
                    ->rules([
                        'numeric',
                        'min:1',
                        'max:'.($this->getOwnerRecord()->product->tracking == ProductTracking::SERIAL ? '1' : '999999999'),
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
                            ->title(__('projects::filament/resources/task.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/resources/task.table.actions.delete.notification.body'))
                            ->success()
                            ->send();
                    }),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.actions.delete.notification.body')),
                    ),
            ])
            ->paginated(false);
    }
}
