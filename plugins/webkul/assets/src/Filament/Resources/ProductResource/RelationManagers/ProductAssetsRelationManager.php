<?php

namespace Webkul\Asset\Filament\Resources\ProductResource\RelationManagers;

use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\AssetExporter;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Asset\Filament\Resources\ProductResource;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;

class ProductAssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'assets';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('assets::filament/resources/product.relation-manager.assets.title');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof AssetProduct) {
            return false;
        }

        return ProductResource::canView($ownerRecord);
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
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['warehouse', 'currentEmployee', 'currentSite']))
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->recordUrl(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record]))
            ->columns([
                TextColumn::make('asset_tag')
                    ->label(__('assets::filament/resources/asset.table.columns.asset-tag'))
                    ->url(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record]))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('serial_no')
                    ->label(__('assets::filament/resources/asset.table.columns.serial-no'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('assets::filament/resources/asset.table.columns.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label(__('assets::filament/resources/asset.table.columns.quantity'))
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),

                TextColumn::make('condition')
                    ->label(__('assets::filament/resources/asset.table.columns.condition'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('cost')
                    ->label(__('assets::filament/resources/asset.table.columns.cost'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('warehouse.name')
                    ->label(__('assets::filament/resources/asset.table.columns.warehouse'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('currentEmployee.name')
                    ->label(__('assets::filament/resources/asset.table.columns.current-employee'))
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('currentSite.full_name')
                    ->label(__('assets::filament/resources/asset.table.columns.current-site'))
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('assets::filament/resources/asset.table.filters.status'))
                    ->options(AssetStatus::class)
                    ->multiple(),

                SelectFilter::make('condition')
                    ->label(__('assets::filament/resources/asset.table.filters.condition'))
                    ->options(AssetCondition::class)
                    ->multiple(),

                SelectFilter::make('warehouse_id')
                    ->label(__('assets::filament/resources/asset.table.filters.warehouse'))
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('current_employee_id')
                    ->label(__('assets::filament/resources/asset.table.filters.current-employee'))
                    ->relationship('currentEmployee', 'name')
                    ->searchable()
                    ->multiple(),

                SelectFilter::make('current_site_id')
                    ->label(__('assets::filament/resources/asset.table.filters.current-site'))
                    ->relationship('currentSite', 'full_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => filled($data['values'] ?? $data['value'] ?? null)
                        ? AssetSite::applySubtreeFilter($query, $data['values'] ?? $data['value'], 'current_site_id')
                        : $query),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record])),
            ])
            ->toolbarActions([
                AssetExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->options(['context' => $owner instanceof AssetProduct ? $owner->name : null])
                    ->modifyQueryUsing(fn (Builder $query, array $options): Builder => AssetExporter::applyExportOptions($query, $options))
                    ->visible(fn (): bool => AssetResource::canViewAny())
                    ->authorize(fn (): bool => AssetResource::canViewAny())),
            ]);
    }
}
