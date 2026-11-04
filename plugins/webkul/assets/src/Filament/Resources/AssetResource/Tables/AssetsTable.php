<?php

namespace Webkul\Asset\Filament\Resources\AssetResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
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
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetProductType;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Filament\Exports\AssetExporter;
use Webkul\Asset\Filament\Resources\AssetResource;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\IssueAssetAction;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\ReturnAssetAction;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\TerminateAssetAction;
use Webkul\Asset\Filament\Resources\AssetResource\Actions\TransferAssetAction;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Asset\Models\AssetSite;

class AssetsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->extraAttributes(['class' => 'fi-ta-sticky-scroll'])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('asset_tag')
                    ->label(__('assets::filament/resources/asset.table.columns.asset-tag'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('product.name')
                    ->label(__('assets::filament/resources/asset.table.columns.product'))
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('product.type')
                    ->label(__('assets::filament/resources/asset.table.columns.product-type'))
                    ->badge()
                    ->toggleable(),

                TextColumn::make('product.model')
                    ->label(__('assets::filament/resources/asset.table.columns.model'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('product.category.full_name')
                    ->label(__('assets::filament/resources/asset.table.columns.category'))
                    ->badge()
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('serial_no')
                    ->label(__('assets::filament/resources/asset.table.columns.serial-no'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label(__('assets::filament/resources/asset.table.columns.quantity'))
                    ->numeric(decimalPlaces: 0)
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('assets::filament/resources/asset.table.columns.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('condition')
                    ->label(__('assets::filament/resources/asset.table.columns.condition'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('warehouse.name')
                    ->label(__('assets::filament/resources/asset.table.columns.warehouse'))
                    ->placeholder('—')
                    ->sortable()
                    ->wrap(),

                TextColumn::make('currentEmployee.name')
                    ->label(__('assets::filament/resources/asset.table.columns.current-employee'))
                    ->placeholder('—')
                    ->sortable()
                    ->wrap(),

                TextColumn::make('currentSite.full_name')
                    ->label(__('assets::filament/resources/asset.table.columns.current-site'))
                    ->placeholder('—')
                    ->sortable()
                    ->wrap(),

                TextColumn::make('cost')
                    ->label(__('assets::filament/resources/asset.table.columns.cost'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('company.name')
                    ->label(__('assets::filament/resources/asset.table.columns.company'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->label(__('assets::filament/resources/asset.table.columns.deleted-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('assets::filament/resources/asset.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('assets::filament/resources/asset.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('product.name')
                    ->label(__('assets::filament/resources/asset.table.groups.product')),
                Group::make('product.category.full_name')
                    ->label(__('assets::filament/resources/asset.table.groups.category')),
                Group::make('status')
                    ->label(__('assets::filament/resources/asset.table.groups.status')),
                Group::make('condition')
                    ->label(__('assets::filament/resources/asset.table.groups.condition')),
                Group::make('warehouse.name')
                    ->label(__('assets::filament/resources/asset.table.groups.warehouse')),
                Group::make('currentEmployee.name')
                    ->label(__('assets::filament/resources/asset.table.groups.current-employee')),
                Group::make('currentSite.full_name')
                    ->label(__('assets::filament/resources/asset.table.groups.current-site')),
            ])
            ->filters([
                SelectFilter::make('product_type')
                    ->label(__('assets::filament/resources/asset.table.filters.product-type'))
                    ->options(AssetProductType::class)
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('product', fn (Builder $q) => $q->where('type', $data['value']))
                        : $query),

                SelectFilter::make('status')
                    ->label(__('assets::filament/resources/asset.table.filters.status'))
                    ->options(AssetStatus::class)
                    ->multiple(),

                SelectFilter::make('condition')
                    ->label(__('assets::filament/resources/asset.table.filters.condition'))
                    ->options(AssetCondition::class)
                    ->multiple(),

                SelectFilter::make('category_id')
                    ->label(__('assets::filament/resources/asset.table.filters.category'))
                    ->relationship('product.category', 'full_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => filled($data['values'] ?? $data['value'] ?? null)
                        ? $query->whereHas('product', fn (Builder $q) => AssetCategory::applySubtreeFilter($q, $data['values'] ?? $data['value'], 'category_id'))
                        : $query),

                SelectFilter::make('current_employee_id')
                    ->label(__('assets::filament/resources/asset.table.filters.current-employee'))
                    ->relationship('currentEmployee', 'name')
                    ->searchable()
                    ->multiple(),

                SelectFilter::make('department_id')
                    ->label(__('assets::filament/resources/asset.table.filters.department'))
                    ->relationship('currentEmployee.department', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('warehouse_id')
                    ->label(__('assets::filament/resources/asset.table.filters.warehouse'))
                    ->relationship('warehouse', 'name')
                    ->searchable()
                    ->preload()
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

                SelectFilter::make('site_usage_type')
                    ->label(__('assets::filament/resources/asset.table.filters.site-usage-type'))
                    ->options(AssetSiteUsage::class)
                    ->query(fn (Builder $query, array $data): Builder => filled($data['value'] ?? null)
                        ? $query->whereHas('currentSite', fn (Builder $q) => $q->where('usage_type', $data['value']))
                        : $query),

                SelectFilter::make('site_type_id')
                    ->label(__('assets::filament/resources/asset.table.filters.site-type'))
                    ->relationship('currentSite.siteType', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('product_id')
                    ->label(__('assets::filament/resources/asset.table.filters.product'))
                    ->relationship('product', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),

                SelectFilter::make('company_id')
                    ->label(__('assets::filament/resources/asset.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()->hidden(fn (Asset $record): bool => $record->trashed()),
                EditAction::make()
                    ->hidden(fn (Asset $record): bool => $record->trashed())
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('assets::filament/resources/asset.table.actions.edit.notification.title'))
                            ->body(__('assets::filament/resources/asset.table.actions.edit.notification.body')),
                    ),
                ActionGroup::make([
                    IssueAssetAction::make(),
                    TransferAssetAction::make(),
                    ReturnAssetAction::make(),
                    TerminateAssetAction::make('scrap', AssetStatus::Scrapped),
                    TerminateAssetAction::make('markLost', AssetStatus::Lost),
                    RestoreAction::make(),
                    DeleteAction::make()
                        ->using(function (Asset $record, DeleteAction $action): void {
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
                        ->using(function (Asset $record, ForceDeleteAction $action): void {
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
                    AssetExporter::configureExportAction(ExportBulkAction::make()
                        ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                        ->icon('heroicon-o-arrow-up-tray')
                        ->exporter(AssetExporter::class)
                        ->columnMappingColumns(2)
                        ->modalWidth('2xl')
                        ->chunkSize(250)
                        ->modifyQueryUsing(fn (Builder $query, array $options): Builder => AssetExporter::applyExportOptions($query, $options))
                        ->visible(fn (): bool => AssetResource::canViewAny())
                        ->authorize(fn (): bool => AssetResource::canViewAny())),
                ]),
                AssetExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->modifyQueryUsing(fn (Builder $query, array $options): Builder => AssetExporter::applyExportOptions($query, $options))
                    ->visible(fn (): bool => AssetResource::canViewAny())
                    ->authorize(fn (): bool => AssetResource::canViewAny())),
            ]);
    }
}
