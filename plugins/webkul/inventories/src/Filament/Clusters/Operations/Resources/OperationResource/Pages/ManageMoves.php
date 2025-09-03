<?php

namespace Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Inventory\Enums;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\OperationResource;
use Webkul\Inventory\Models\MoveLine;
use Webkul\Inventory\Settings\OperationSettings;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Inventory\Settings\WarehouseSettings;

class ManageMoves extends ManageRelatedRecords
{
    protected static string $resource = OperationResource::class;

    protected static string $relationship = 'moveLines';

    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';

    public static function getNavigationLabel(): string
    {
        return __('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.columns.date'))
                    ->sortable()
                    ->dateTime(),
                Tables\Columns\TextColumn::make('reference')
                    ->label(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.columns.reference'))
                    ->searchable(['inventories_moves.reference'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('lot.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.columns.lot'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(fn (TraceabilitySettings $settings) => $settings->enable_lots_serial_numbers && $this->getOwnerRecord()->tracking != Enums\ProductTracking::QTY),
                Tables\Columns\TextColumn::make('package.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.columns.package'))
                    ->sortable()
                    ->placeholder('—')
                    ->visible(fn (OperationSettings $settings) => $settings->enable_packages),
                Tables\Columns\TextColumn::make('sourceLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.columns.source-location'))
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                Tables\Columns\TextColumn::make('destinationLocation.full_name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.columns.destination-location'))
                    ->visible(fn (WarehouseSettings $settings) => $settings->enable_locations),
                Tables\Columns\TextColumn::make('uom_qty')
                    ->label(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.columns.quantity'))
                    ->sortable()
                    ->color(fn ($record) => $record->destinationLocation->type == Enums\LocationType::INTERNAL ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('state')
                    ->label(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.columns.state'))
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('creator.name')
                    ->label(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.columns.done-by'))
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (MoveLine $record): bool => $record->state == Enums\MoveState::DONE)
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.actions.delete.notification.title'))
                            ->body(__('inventories::filament/clusters/operations/resources/operation/pages/manage-moves.table.actions.delete.notification.body')),
                    ),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
