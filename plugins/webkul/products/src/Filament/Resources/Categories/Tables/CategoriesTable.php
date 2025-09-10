<?php

namespace Webkul\Product\Filament\Resources\Categories\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Product\Models\Category;

class CategoriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('products::filament/resources/category.table.columns.name'))
                    ->searchable(),
                TextColumn::make('full_name')
                    ->label(__('products::filament/resources/category.table.columns.full-name'))
                    ->searchable(),
                TextColumn::make('parent_path')
                    ->label(__('products::filament/resources/category.table.columns.parent-path'))
                    ->placeholder('—')
                    ->searchable(),
                TextColumn::make('parent.name')
                    ->label(__('products::filament/resources/category.table.columns.parent'))
                    ->placeholder('—')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('products::filament/resources/category.table.columns.creator'))
                    ->placeholder('—')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('products::filament/resources/category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/category.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('parent.full_name')
                    ->label(__('products::filament/resources/category.table.groups.parent'))
                    ->collapsible(),
                Tables\Grouping\Group::make('creator.name')
                    ->label(__('products::filament/resources/category.table.groups.creator'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('products::filament/resources/category.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('products::filament/resources/category.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('parent_id')
                    ->label(__('products::filament/resources/category.table.filters.parent'))
                    ->relationship('parent', 'full_name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('products::filament/resources/category.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->action(function (Category $record) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('products::filament/resources/category.table.actions.delete.notification.error.title'))
                                ->body(__('products::filament/resources/category.table.actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/category.table.actions.delete.notification.success.title'))
                            ->body(__('products::filament/resources/category.table.actions.delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                DeleteBulkAction::make()
                    ->action(function (Collection $records) {
                        try {
                            $records->each(fn (Model $record) => $record->delete());
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('products::filament/resources/category.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('products::filament/resources/category.table.bulk-actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/category.table.bulk-actions.delete.notification.success.title'))
                            ->body(__('products::filament/resources/category.table.bulk-actions.delete.notification.success.body')),
                    ),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
