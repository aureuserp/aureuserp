<?php

namespace Webkul\Sale\Filament\Clusters\Configuration\Resources\Tags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('sales::filament/clusters/configurations/resources/tag.table.columns.name')),
                ColorColumn::make('color')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.table.columns.color')),
                TextColumn::make('createdBy.name')
                    ->label(__('sales::filament/clusters/configurations/resources/tag.table.columns.created-by')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/tag.table.actions.edit.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/tag.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/configurations/resources/tag.table.actions.delete.notification.title'))
                            ->body(__('sales::filament/clusters/configurations/resources/tag.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('sales::filament/clusters/configurations/resources/tag.table.bulk-actions.delete.notification.title'))
                                ->body(__('sales::filament/clusters/configurations/resources/tag.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
