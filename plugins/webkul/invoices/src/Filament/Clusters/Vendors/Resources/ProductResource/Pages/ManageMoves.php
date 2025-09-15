<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ProductResource;

class ManageMoves extends ManageRelatedRecords
{
    protected static string $resource = ProductResource::class;

    protected static string $relationship = 'moveLines';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getSubNavigationPosition(): SubNavigationPosition
    {
        return SubNavigationPosition::Top;
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.columns.date'))
                    ->sortable()
                    ->dateTime(),
                TextColumn::make('reference')
                    ->label(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.columns.reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('lot.name')
                    ->label(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.columns.lot'))
                    ->sortable()
                    ->visible(fn (TraceabilitySettings $traceabilitySettings) => $traceabilitySettings->enable_lots_serial_numbers && $this->getOwnerRecord()->tracking != ProductTracking::QTY),
                TextColumn::make('resultPackage.name')
                    ->label(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.columns.package'))
                    ->sortable()
                    ->visible(fn (OperationSettings $operationSettings) => $operationSettings->enable_packages),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.columns.source-location'))
                    ->visible(fn (WarehouseSettings $warehouseSettings) => $warehouseSettings->enable_locations),
                TextColumn::make('destinationLocation.full_name')
                    ->label(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.columns.destination-location'))
                    ->visible(fn (WarehouseSettings $warehouseSettings) => $warehouseSettings->enable_locations),
                TextColumn::make('qty')
                    ->label(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.columns.quantity'))
                    ->sortable(),
                TextColumn::make('state')
                    ->label(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.columns.state'))
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.columns.done-by'))
                    ->sortable(),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.actions.delete.notification.title'))
                            ->body(__('invoices::filament/clusters/vendors/resources/product/pages/manage-moves.table.actions.delete.notification.body')),
                    ),
            ]);
    }
}
