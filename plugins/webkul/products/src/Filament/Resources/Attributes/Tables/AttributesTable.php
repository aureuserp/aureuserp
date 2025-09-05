<?php

namespace Webkul\Product\Filament\Resources\Attributes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Product\Enums\AttributeType;
use Webkul\Product\Models\Attribute;

class AttributesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('products::filament/resources/attribute.table.columns.name'))
                    ->searchable(),
                TextColumn::make('type')
                    ->label(__('products::filament/resources/attribute.table.columns.type'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('products::filament/resources/attribute.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/attribute.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('type')
                    ->label(__('products::filament/resources/attribute.table.groups.type'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('products::filament/resources/attribute.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('products::filament/resources/attribute.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('products::filament/resources/attribute.table.filters.type'))
                    ->options(AttributeType::class)
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                EditAction::make()
                    ->hidden(fn ($record) => $record->trashed()),
                RestoreAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.restore.notification.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.restore.notification.body')),
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.delete.notification.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.delete.notification.body')),
                    ),
                ForceDeleteAction::make()
                    ->action(function (Attribute $record) {
                        try {
                            $record->forceDelete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('products::filament/resources/attribute.table.actions.force-delete.notification.error.title'))
                                ->body(__('products::filament/resources/attribute.table.actions.force-delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('products::filament/resources/attribute.table.actions.force-delete.notification.success.title'))
                            ->body(__('products::filament/resources/attribute.table.actions.force-delete.notification.success.body')),
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.restore.notification.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.restore.notification.body')),
                        ),
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.delete.notification.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.success.title'))
                                ->body(__('products::filament/resources/attribute.table.bulk-actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
