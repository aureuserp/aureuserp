<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\RelationManagers;

use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Enums\AssetSourceType;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Filament\Exports\AssetMovementExporter;
use Webkul\Asset\Filament\Resources\AssetMovementResource;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Asset\Models\Asset;

class AssignmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'movements';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('assets::filament/resources/asset.relation-manager.assignments.title');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof Asset) {
            return false;
        }

        return AssetResource::canView($ownerRecord);
    }

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();

        return $table
            ->extraAttributes(['class' => 'fi-ta-sticky-scroll'])
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['fromWarehouse', 'fromEmployee', 'fromSite', 'toWarehouse', 'toEmployee', 'toSite', 'performedBy']))
            ->defaultSort('occurred_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('occurred_at')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.columns.occurred-at'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.columns.type'))
                    ->badge(),

                TextColumn::make('quantity')
                    ->label(__('assets::filament/resources/asset-movement.table.columns.quantity'))
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),

                TextColumn::make('source_type')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.columns.source-type'))
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('from_display')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.columns.from-display'))
                    ->placeholder('—'),

                TextColumn::make('to_display')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.columns.to-display'))
                    ->placeholder('—'),

                TextColumn::make('condition')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.columns.condition'))
                    ->badge(),

                TextColumn::make('performedBy.name')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.columns.performed-by'))
                    ->placeholder('—'),

                TextColumn::make('expected_return_at')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.columns.expected-return-at'))
                    ->date()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('note')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.columns.note'))
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.filters.type'))
                    ->options(MovementType::class),

                SelectFilter::make('source_type')
                    ->label(__('assets::filament/resources/asset.relation-manager.assignments.filters.source-type'))
                    ->options(AssetSourceType::class),
            ])
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->modalHeading(__('assets::filament/resources/asset.relation-manager.assignments.view-modal.heading'))
                    ->schema(AssetMovementResource::movementModalSchema(includeAsset: false)),
            ])
            ->toolbarActions([
                AssetMovementExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset-movement.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetMovementExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->options(['context' => $owner instanceof Asset ? $owner->asset_tag : null])
                    ->visible(fn (): bool => AssetMovementResource::canViewAny())
                    ->authorize(fn (): bool => AssetMovementResource::canViewAny())
                    ->modifyQueryUsing(fn (Builder $query, array $options): Builder => AssetMovementExporter::applyExportOptions($query, $options))),
            ]);
    }
}
