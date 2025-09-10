<?php

namespace Webkul\Account\Filament\Resources\FiscalPositions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FiscalPositionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.name')),
                TextColumn::make('company.name')
                    ->searchable()
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.company')),
                TextColumn::make('country.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.country')),
                TextColumn::make('countryGroup.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.country-group')),
                TextColumn::make('createdBy.name')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.created-by')),
                TextColumn::make('zip_from')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.zip-from')),
                TextColumn::make('zip_to')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.zip-to')),
                IconColumn::make('auto_reply')
                    ->searchable()
                    ->sortable()
                    ->label(__('Detect Automatically'))
                    ->label(__('accounts::filament/resources/fiscal-position.table.columns.detect-automatically')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/fiscal-position.table.columns.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/fiscal-position.table.columns.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/fiscal-position.table.columns.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/fiscal-position.table.columns.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ]);
    }
}
