<?php

namespace Webkul\Asset\Filament\Resources\AssetCategoryResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ExportBulkAction;
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
use Webkul\Asset\Filament\Exports\AssetCategoryExporter;
use Webkul\Asset\Filament\Resources\AssetCategoryResource;
use Webkul\Asset\Models\AssetCategory;

class AssetCategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->extraAttributes(['class' => 'fi-ta-sticky-scroll'])
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->columns([
                TextColumn::make('name')
                    ->label(__('assets::filament/resources/category.table.columns.name'))
                    ->searchable(['name', 'full_name'])
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('full_name')
                    ->label(__('assets::filament/resources/category.table.columns.full-name'))
                    ->sortable()
                    ->wrap(),

                TextColumn::make('parent.full_name')
                    ->label(__('assets::filament/resources/category.table.columns.parent'))
                    ->badge()
                    ->placeholder('—')
                    ->sortable(),

                TextColumn::make('products_count')
                    ->label(__('assets::filament/resources/category.table.columns.products-count'))
                    ->counts('products')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('assets_count')
                    ->label(__('assets::filament/resources/category.table.columns.assets-count'))
                    ->counts('assets')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                TextColumn::make('assets_sum_cost')
                    ->label(__('assets::filament/resources/category.table.columns.total-cost'))
                    ->sum('assets', 'cost')
                    ->numeric(decimalPlaces: 2)
                    ->placeholder('0.00')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('company.name')
                    ->label(__('assets::filament/resources/category.table.columns.company'))
                    ->sortable(),

                TextColumn::make('creator.name')
                    ->label(__('assets::filament/resources/category.table.columns.creator'))
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('assets::filament/resources/category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('assets::filament/resources/category.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('parent.full_name')
                    ->label(__('assets::filament/resources/category.table.filters.parent')),
                Group::make('company.name')
                    ->label(__('assets::filament/resources/category.table.columns.company')),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label(__('assets::filament/resources/category.table.filters.parent'))
                    ->relationship('parent', 'full_name')
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => filled($data['values'] ?? $data['value'] ?? null)
                        ? AssetCategory::applySubtreeFilter($query, $data['values'] ?? $data['value'], 'id')
                        : $query),

                SelectFilter::make('company_id')
                    ->label(__('assets::filament/resources/category.table.filters.company'))
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('assets::filament/resources/category.table.actions.edit.notification.title'))
                            ->body(__('assets::filament/resources/category.table.actions.edit.notification.body')),
                    ),
                ActionGroup::make([
                    DeleteAction::make()
                        ->using(function (AssetCategory $record, DeleteAction $action): void {
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
                    AssetCategoryExporter::configureExportAction(ExportBulkAction::make()
                        ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                        ->icon('heroicon-o-arrow-up-tray')
                        ->exporter(AssetCategoryExporter::class)
                        ->columnMappingColumns(2)
                        ->modalWidth('2xl')
                        ->chunkSize(250)
                        ->visible(fn (): bool => AssetCategoryResource::canViewAny())
                        ->authorize(fn (): bool => AssetCategoryResource::canViewAny())),
                ]),
                AssetCategoryExporter::configureExportAction(ExportAction::make()
                    ->label(__('assets::filament/resources/asset.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AssetCategoryExporter::class)
                    ->columnMappingColumns(2)
                    ->modalWidth('2xl')
                    ->chunkSize(250)
                    ->visible(fn (): bool => AssetCategoryResource::canViewAny())
                    ->authorize(fn (): bool => AssetCategoryResource::canViewAny())),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
