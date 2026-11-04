<?php

namespace Webkul\Asset\Filament\Resources\AssetWarehouseResource\RelationManagers;

use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\AssetExporter;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\IssueAssetAction;
use Webkul\Asset\Filament\Resources\AssetWarehouseResource;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetWarehouse;

class WarehouseAssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('assets::filament/resources/asset-warehouse.relation-manager.warehouse-assets.title');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof AssetWarehouse) {
            return false;
        }

        return AssetWarehouseResource::canView($ownerRecord);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();

        return $table
            ->extraAttributes(['class' => 'fi-ta-sticky-scroll'])
            ->modifyQueryUsing(fn (Builder $query) => $query
                ->where('status', AssetStatus::Available)
                ->with(['product.category', 'warehouse.custodian', 'currentEmployee', 'currentSite.defaultCustodian']))
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('asset_tag')
                    ->label(__('assets::filament/resources/asset-warehouse.relation-manager.warehouse-assets.columns.asset-tag'))
                    ->url(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record]))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('product.name')
                    ->label(__('assets::filament/resources/asset-warehouse.relation-manager.warehouse-assets.columns.product'))
                    ->url(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record]))
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('product.model')
                    ->label(__('assets::filament/resources/asset-warehouse.relation-manager.warehouse-assets.columns.model'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('product.category.full_name')
                    ->label(__('assets::filament/resources/asset-warehouse.relation-manager.warehouse-assets.columns.category'))
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('serial_no')
                    ->label(__('assets::filament/resources/asset-warehouse.relation-manager.warehouse-assets.columns.serial-no'))
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('quantity')
                    ->label(__('assets::filament/resources/asset.table.columns.quantity'))
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),

                TextColumn::make('condition')
                    ->label(__('assets::filament/resources/asset-warehouse.relation-manager.warehouse-assets.columns.condition'))
                    ->badge(),

                TextColumn::make('cost')
                    ->label(__('assets::filament/resources/asset-warehouse.relation-manager.warehouse-assets.columns.cost'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('purchase_date')
                    ->label(__('assets::filament/resources/asset-warehouse.relation-manager.warehouse-assets.columns.purchase-date'))
                    ->date()
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record])),
                IssueAssetAction::make(),
            ])
            ->toolbarActions([
                AssetExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->options(['context' => $owner instanceof AssetWarehouse ? $owner->name : null])
                    ->modifyQueryUsing(fn (Builder $query, array $options): Builder => AssetExporter::applyExportOptions($query, $options))
                    ->visible(fn (): bool => AssetResource::canViewAny())
                    ->authorize(fn (): bool => AssetResource::canViewAny())),
            ]);
    }
}
