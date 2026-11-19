<?php

namespace Webkul\Asset\Filament\Resources\AssetSiteResource\RelationManagers;

use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\EmbeddedTable;
use Filament\Schemas\Components\RenderHook;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Filament\View\PanelsRenderHook;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\AssetExporter;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\ReturnAssetAction;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\TransferAssetAction;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetSite;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class SiteAssetsRelationManager extends RelationManager
{
    use HasTableViews;

    protected static string $relationship = 'assets';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('assets::filament/resources/asset-site.relation-manager.site-assets.title');
    }

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        if (! $ownerRecord instanceof AssetSite) {
            return false;
        }

        return AssetSiteResource::canView($ownerRecord);
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    /**
     * @return array<string, PresetView>
     */
    public function getPresetTableViews(): array
    {
        $owner = $this->getOwnerRecord();

        if (! $owner instanceof AssetSite) {
            return [];
        }

        $subtreeIds = AssetSite::subtreeIds((int) $owner->getKey());

        $counts = Asset::query()
            ->where('status', AssetStatus::Held)
            ->whereIn('current_site_id', $subtreeIds)
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN current_site_id = ? THEN 1 ELSE 0 END) as direct', [$owner->id])
            ->first();

        $total = (int) ($counts->total ?? 0);
        $direct = (int) ($counts->direct ?? 0);
        $subsites = max(0, $total - $direct);

        $views = [
            'all' => PresetView::make(__('assets::filament/resources/asset-site.relation-manager.site-assets.tabs.all'))
                ->icon('heroicon-s-bars-3')
                ->favorite()
                ->setAsDefault()
                ->badge($total),
        ];

        if (count($subtreeIds) > 1) {
            $views['direct'] = PresetView::make(__('assets::filament/resources/asset-site.relation-manager.site-assets.tabs.direct'))
                ->icon('heroicon-s-building-office-2')
                ->favorite()
                ->badge($direct)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('current_site_id', $owner->id));

            $views['subsites'] = PresetView::make(__('assets::filament/resources/asset-site.relation-manager.site-assets.tabs.subsites'))
                ->icon('heroicon-s-rectangle-stack')
                ->favorite()
                ->badge($subsites)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('current_site_id', '!=', $owner->id));
        }

        return $views;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Text::make(fn (): string => static::getTitle($this->getOwnerRecord(), $this->getPageClass()))
                    ->size(TextSize::Large)
                    ->weight(FontWeight::Bold),
                $this->getTabsContentComponent(),
                RenderHook::make(PanelsRenderHook::RESOURCE_RELATION_MANAGER_BEFORE),
                EmbeddedTable::make(),
                RenderHook::make(PanelsRenderHook::RESOURCE_RELATION_MANAGER_AFTER),
            ]);
    }

    public function table(Table $table): Table
    {
        $owner = $this->getOwnerRecord();

        return $table
            ->extraAttributes(['class' => 'fi-ta-sticky-scroll'])
            ->heading(null)
            ->query(fn (): Builder => Asset::query()
                ->where('status', AssetStatus::Held)
                ->whereIn('current_site_id', AssetSite::subtreeIds((int) $owner->getKey()))
                ->with(['product.category', 'currentEmployee', 'currentSite.defaultCustodian', 'warehouse.custodian']))
            ->defaultSort('issued_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('asset_tag')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.asset-tag'))
                    ->url(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record]))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('product.name')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.asset-name'))
                    ->url(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record]))
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('product.category.full_name')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.category'))
                    ->badge()
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('currentSite.full_name')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.current-site'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('serial_no')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.serial-no'))
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('currentEmployee.name')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.receiver'))
                    ->searchable()
                    ->placeholder('—'),

                TextColumn::make('condition')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.condition-out'))
                    ->badge(),

                TextColumn::make('cost')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.cost'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('issued_at')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.issued-at'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('expected_return_at')
                    ->label(__('assets::filament/resources/asset-site.relation-manager.site-assets.columns.expected-return-at'))
                    ->date()
                    ->placeholder('—'),
            ])
            ->groups([
                Group::make('currentSite.full_name')
                    ->label(__('assets::filament/resources/asset.table.groups.current-site')),
                Group::make('product.category.full_name')
                    ->label(__('assets::filament/resources/asset.table.groups.category')),
                Group::make('product.name')
                    ->label(__('assets::filament/resources/asset.table.groups.product')),
                Group::make('condition')
                    ->label(__('assets::filament/resources/asset.table.groups.condition')),
                Group::make('currentEmployee.name')
                    ->label(__('assets::filament/resources/asset.table.groups.current-employee')),
            ])
            ->filters([
                SelectFilter::make('current_site_id')
                    ->label(__('assets::filament/resources/asset.table.filters.current-site'))
                    ->relationship(
                        'currentSite',
                        'full_name',
                        modifyQueryUsing: fn (Builder $query): Builder => $query
                            ->whereIn('id', AssetSite::subtreeIds((int) $this->getOwnerRecord()->getKey()))
                            ->orderBy('full_name'),
                    )
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => filled($data['values'] ?? $data['value'] ?? null)
                        ? AssetSite::applySubtreeFilter($query, $data['values'] ?? $data['value'], 'current_site_id')
                        : $query),

                SelectFilter::make('category_id')
                    ->label(__('assets::filament/resources/asset.table.filters.category'))
                    ->relationship('product.category', 'full_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => filled($data['values'] ?? $data['value'] ?? null)
                        ? $query->whereHas('product', fn (Builder $q) => AssetCategory::applySubtreeFilter($q, $data['values'] ?? $data['value'], 'category_id'))
                        : $query),

                SelectFilter::make('product_id')
                    ->label(__('assets::filament/resources/asset.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('condition')
                    ->label(__('assets::filament/resources/asset.table.filters.condition'))
                    ->options(AssetCondition::class)
                    ->multiple(),

                SelectFilter::make('current_employee_id')
                    ->label(__('assets::filament/resources/asset.table.filters.current-employee'))
                    ->relationship('currentEmployee', 'name')
                    ->searchable()
                    ->multiple(),
            ])
            ->headerActions([])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Asset $record): string => AssetResource::getUrl('view', ['record' => $record])),
                TransferAssetAction::make(),
                ReturnAssetAction::make(),
            ])
            ->toolbarActions([
                AssetExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->options(['context' => $owner instanceof AssetSite ? $owner->full_name : null])
                    ->modifyQueryUsing(fn (Builder $query, array $options): Builder => AssetExporter::applyExportOptions($query, $options))
                    ->visible(fn (): bool => AssetResource::canViewAny())
                    ->authorize(fn (): bool => AssetResource::canViewAny())),
            ]);
    }
}
