<?php

namespace Webkul\Website\Filament\Admin\Resources\Page\Tables;

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

class PageTable
{
    public static function configure($table)
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('website::filament/admin/resources/page.table.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('website::filament/admin/resources/page.table.columns.slug'))
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('website::filament/admin/resources/page.table.columns.creator'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_published')
                    ->label(__('website::filament/admin/resources/page.table.columns.is-published'))
                    ->boolean()
                    ->sortable(),
                IconColumn::make('is_header_visible')
                    ->label(__('website::filament/admin/resources/page.table.columns.is-header-visible'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_footer_visible')
                    ->label(__('website::filament/admin/resources/page.table.columns.is-footer-visible'))
                    ->boolean()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('website::filament/admin/resources/page.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('website::filament/admin/resources/page.table.columns.created-at'))
                    ->sortable(),
            ])
            ->groups([
                Tables\Grouping\Group::make('created_at')
                    ->label(__('website::filament/admin/resources/page.table.groups.created-at'))
                    ->date(),
            ])
            ->filters([
                Filter::make('is_published')
                    ->label(__('website::filament/admin/resources/page.table.filters.is-published')),
                SelectFilter::make('creator_id')
                    ->label(__('website::filament/admin/resources/page.table.filters.creator'))
                    ->relationship('creator', 'name')
                    ->searchable()
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
                                ->title(__('website::filament/admin/resources/page.table.actions.restore.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.actions.restore.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.actions.delete.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.actions.delete.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.actions.force-delete.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.actions.force-delete.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.bulk-actions.restore.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.bulk-actions.delete.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('website::filament/admin/resources/page.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('website::filament/admin/resources/page.table.bulk-actions.force-delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
