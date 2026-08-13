<?php

namespace Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Table;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Filament\Clusters\Products\Resources\LotResource;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Product\Settings\ProductSettings;
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

        return static::getOperationSettings()->enable_packages
            || static::getWarehouseSettings()->enable_locations
            || (
                static::getTraceabilitySettings()->enable_lots_serial_numbers
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
                    ->visible(static::getWarehouseSettings()->enable_locations),
                TextColumn::make('storageCategory.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.columns.storage-category'))
                    ->placeholder('—'),
                TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.columns.package'))
                    ->placeholder('—')
                    ->visible(static::getOperationSettings()->enable_packages),
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

                        $record->update([
                            'inventory_diff_quantity' => $state - $previousQuantity,
                        ]);
                    })
                    ->afterStateUpdated(function ($record, $state) {
                        Notification::make()
                            ->success()
                            ->title(__('projects::filament/resources/task.table.actions.delete.notification.title'))
                            ->body(__('projects::filament/resources/task.table.actions.delete.notification.body'))
                            ->success()
                            ->send();
                    }),
                TextColumn::make('product.uom.name')
                    ->label(__('inventories::filament/clusters/products/resources/lot/pages/manage-quantities.table.columns.unit'))
                    ->placeholder('—')
                    ->visible(static::getProductSettings()->enable_uom),
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
