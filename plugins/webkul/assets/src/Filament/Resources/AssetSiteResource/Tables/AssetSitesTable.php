<?php

namespace Webkul\Asset\Filament\Resources\AssetSiteResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\AssetSiteExporter;
use Webkul\Asset\Filament\Resources\AssetSiteResource;
use Webkul\Asset\Models\AssetSite;

class AssetSitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->extraAttributes(['class' => 'fi-ta-sticky-scroll'])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('full_name')
                    ->label(__('assets::filament/resources/asset-site.table.columns.full-name'))
                    ->searchable(['name', 'full_name'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('usage_type')
                    ->label(__('assets::filament/resources/asset-site.table.columns.usage-type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('code')
                    ->label(__('assets::filament/resources/asset-site.table.columns.code'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('siteType.name')
                    ->label(__('assets::filament/resources/asset-site.table.columns.type'))
                    ->badge()
                    ->icon(fn (AssetSite $record): ?string => $record->siteType?->icon)
                    ->color(fn (AssetSite $record): string|array => filled($record->siteType?->color) ? Color::hex($record->siteType->color) : 'gray')
                    ->sortable(),

                TextColumn::make('defaultCustodian.name')
                    ->label(__('assets::filament/resources/asset-site.table.columns.default-custodian'))
                    ->placeholder('—')
                    ->sortable()
                    ->searchable(),

                IconColumn::make('is_active')
                    ->label(__('assets::filament/resources/asset-site.table.columns.is-active'))
                    ->boolean(),

                TextColumn::make('assets_count')
                    ->label(__('assets::filament/resources/asset-site.table.columns.assets-count'))
                    ->counts(['assets' => fn (Builder $query) => $query->where('status', AssetStatus::Held)])
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('assets_sum_cost')
                    ->label(__('assets::filament/resources/asset-site.table.columns.total-cost'))
                    ->sum(['assets' => fn (Builder $query) => $query->where('status', AssetStatus::Held)], 'cost')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('0.00')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('company.name')
                    ->label(__('assets::filament/resources/asset-site.table.columns.company'))
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label(__('assets::filament/resources/asset-site.table.columns.creator'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label(__('assets::filament/resources/asset-site.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('assets::filament/resources/asset-site.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('assets::filament/resources/asset-site.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('usage_type')
                    ->label(__('assets::filament/resources/asset-site.table.columns.usage-type')),
                Group::make('parent.full_name')
                    ->label(__('assets::filament/resources/asset-site.table.filters.parent')),
                Group::make('siteType.name')
                    ->label(__('assets::filament/resources/asset-site.table.columns.type')),
            ])
            ->filters([
                SelectFilter::make('usage_type')
                    ->label(__('assets::filament/resources/asset-site.table.filters.usage-type'))
                    ->options(AssetSiteUsage::class),

                SelectFilter::make('parent_id')
                    ->label(__('assets::filament/resources/asset-site.table.filters.parent'))
                    ->relationship('parent', 'full_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => filled($data['values'] ?? $data['value'] ?? null)
                        ? AssetSite::applySubtreeFilter($query, $data['values'] ?? $data['value'], 'id')
                        : $query),

                SelectFilter::make('site_type_id')
                    ->label(__('assets::filament/resources/asset-site.table.filters.type'))
                    ->relationship('siteType', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                TernaryFilter::make('is_active')
                    ->label(__('assets::filament/resources/asset-site.table.filters.is-active')),

                SelectFilter::make('company_id')
                    ->label(__('assets::filament/resources/asset-site.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()->hidden(fn (AssetSite $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn (AssetSite $record): bool => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('assets::filament/resources/asset-site.table.actions.edit.notification.title'))
                            ->body(__('assets::filament/resources/asset-site.table.actions.edit.notification.body')),
                    ),
                ActionGroup::make([
                    RestoreAction::make(),
                    DeleteAction::make()
                        ->using(function (AssetSite $record, DeleteAction $action): void {
                            try {
                                $record->delete();
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(collect($e->errors())->flatten()->first())
                                    ->send();

                                $action->halt();
                            }
                        }),
                    ForceDeleteAction::make()
                        ->using(function (AssetSite $record, ForceDeleteAction $action): void {
                            try {
                                $record->forceDelete();
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(collect($e->errors())->flatten()->first())
                                    ->send();

                                $action->halt();
                            }
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->using(function (Collection $records, DeleteBulkAction $action): void {
                            try {
                                DB::transaction(fn () => $records->each->delete());
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(collect($e->errors())->flatten()->first())
                                    ->send();

                                $action->halt();
                            }
                        }),
                    ForceDeleteBulkAction::make()
                        ->using(function (Collection $records, ForceDeleteBulkAction $action): void {
                            try {
                                DB::transaction(fn () => $records->each->forceDelete());
                            } catch (ValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(collect($e->errors())->flatten()->first())
                                    ->send();

                                $action->halt();
                            }
                        }),
                    RestoreBulkAction::make(),
                    AssetSiteExporter::configureExportAction(ExportBulkAction::make()
                        ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                        ->icon('heroicon-o-arrow-up-tray')
                        ->exporter(AssetSiteExporter::class)
                        ->columnMappingColumns(2)
                        ->modalWidth('2xl')
                        ->chunkSize(250)
                        ->visible(fn (): bool => AssetSiteResource::canViewAny())
                        ->authorize(fn (): bool => AssetSiteResource::canViewAny())),
                ]),
                AssetSiteExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetSiteExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->visible(fn (): bool => AssetSiteResource::canViewAny())
                    ->authorize(fn (): bool => AssetSiteResource::canViewAny())),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
