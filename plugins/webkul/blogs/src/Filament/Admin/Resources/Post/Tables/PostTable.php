<?php

namespace Webkul\Blog\Filament\Admin\Resources\Post\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.slug'))
                    ->sortable(),
                TextColumn::make('author.name')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.author'))
                    ->sortable(),
                TextColumn::make('category.name')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.category'))
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.creator'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_published')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.is-published'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('blogs::filament/admin/resources/post.table.columns.created-at'))
                    ->sortable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('category.name')
                    ->label(__('blogs::filament/admin/resources/post.table.groups.category')),
                Tables\Grouping\Group::make('author.name')
                    ->label(__('blogs::filament/admin/resources/post.table.groups.author')),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('blogs::filament/admin/resources/post.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                Filter::make('is_published')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.is-published')),
                SelectFilter::make('author_id')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.author'))
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('creator_id')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('category_id')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('tags')
                    ->label(__('blogs::filament/admin/resources/post.table.filters.tags'))
                    ->relationship('tags', 'name')
                    ->searchable()
                    ->multiple()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.actions.restore.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.actions.delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.actions.force-delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.bulk-actions.restore.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.bulk-actions.delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('blogs::filament/admin/resources/post.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('blogs::filament/admin/resources/post.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
