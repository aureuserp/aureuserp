<?php

namespace Webkul\Account\Filament\Resources\TaxGroupResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Account\Models\TaxGroup;

class TaxGroupsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.company'))
                    ->sortable(),
                TextColumn::make('country.name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.country'))
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.created-by'))
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.name'))
                    ->searchable(),
                TextColumn::make('preceding_subtotal')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.preceding-subtotal'))
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(__('accounts::filament/resources/tax-group.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/tax-group.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.name'))
                    ->collapsible(),
                Group::make('company.name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.company'))
                    ->collapsible(),
                Group::make('country.name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.country'))
                    ->collapsible(),
                Group::make('creator.name')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.created-by'))
                    ->collapsible(),
                Group::make('created_at')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.created-at'))
                    ->collapsible(),
                Group::make('updated_at')
                    ->label(__('accounts::filament/resources/tax-group.table.groups.updated-at'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->action(function (TaxGroup $record) {
                        try {
                            $record->delete();
                        } catch (QueryException $e) {
                            Notification::make()
                                ->danger()
                                ->title(__('accounts::filament/resources/tax-group.table.actions.delete.notification.error.title'))
                                ->body(__('accounts::filament/resources/tax-group.table.actions.delete.notification.error.body'))
                                ->send();
                        }
                    })
                    ->successNotification(
                        Notification::make()
                            ->title(__('accounts::filament/resources/tax-group.table.actions.delete.notification.success.title'))
                            ->body(__('accounts::filament/resources/tax-group.table.actions.delete.notification.success.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records, DeleteBulkAction $action) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());

                                $action->success();
                            } catch (QueryException $e) {
                                $action->failure();

                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/tax-group.table.bulk-actions.delete.notification.success.body'))
                        ),
                ]),
            ]);
    }
}
