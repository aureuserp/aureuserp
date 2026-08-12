<?php

namespace Webkul\Account\Filament\Resources\AccountResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Models\Account;

class AccountsTable
{
    public static function configure(Table $table, array $customColumns = [], array $customFilters = []): Table
    {
        return $table
            ->columns(array_merge([
                TextColumn::make('code')
                    ->label(__('accounts::filament/resources/account.table.columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/account.table.columns.account-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('account_type')
                    ->label(__('accounts::filament/resources/account.table.columns.account-type'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('parent.name')
                    ->label(__('accounts::filament/resources/account.table.columns.parent-account'))
                    ->placeholder('-')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('reconcile')
                    ->label(__('accounts::filament/resources/account.table.columns.reconcile'))
                    ->boolean()
                    ->sortable(),
                TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/account.table.columns.currency'))
                    ->sortable(),
            ], $customColumns))
            ->groups([
                'account_type',
            ])
            ->filters(array_merge([
                SelectFilter::make('account_type')
                    ->options(AccountType::groupedOptions())
                    ->label(__('accounts::filament/resources/account.table.filters.account-type')),
                SelectFilter::make('parent_id')
                    ->relationship('parent', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('accounts::filament/resources/account.table.filters.parent-account')),
                SelectFilter::make('journals')
                    ->relationship('journals', 'name')
                    ->searchable()
                    ->preload()
                    ->label(__('accounts::filament/resources/account.table.filters.account-journals')),
                SelectFilter::make('currency')
                    ->relationship(name: 'currency', titleAttribute: 'name', modifyQueryUsing: fn (Builder $query) => $query->active())
                    ->searchable()
                    ->preload()
                    ->label(__('accounts::filament/resources/account.table.filters.currency')),
                TernaryFilter::make('reconcile')
                    ->label(__('accounts::filament/resources/account.table.filters.allow-reconcile')),
                TernaryFilter::make('non_trade')
                    ->label(__('accounts::filament/resources/account.table.filters.non-trade')),
            ], $customFilters))
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/account.table.actions.edit.notification.title'))
                            ->body(__('accounts::filament/resources/account.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->action(function (Account $record, DeleteAction $action) {
                        if ($record->moveLines()->count() > 0) {
                            $action->failure();

                            return;
                        }

                        $record->delete();

                        $action->success();
                    })
                    ->failureNotification(
                        Notification::make()
                            ->danger()
                            ->title(__('accounts::filament/resources/account.table.actions.delete.notification.error.title'))
                            ->body(__('accounts::filament/resources/account.table.actions.delete.notification.error.body'))
                    )
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/account.table.actions.delete.notification.success.title'))
                            ->body(__('accounts::filament/resources/account.table.actions.delete.notification.success.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records, DeleteBulkAction $action) {
                            $hasMoveLines = $records->contains(function ($record) {
                                return $record->moveLines()->exists();
                            });

                            if ($hasMoveLines) {
                                $action->failure();

                                return;
                            }

                            $records->each(fn (Model $record) => $record->delete());

                            $action->success();
                        })
                        ->failureNotification(
                            Notification::make()
                                ->danger()
                                ->title(__('accounts::filament/resources/account.table.bulk-actions.delete.notification.error.title'))
                                ->body(__('accounts::filament/resources/account.table.bulk-actions.delete.notification.error.body'))
                        )
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/account.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/account.table.bulk-actions.delete.notification.success.body'))
                        ),
                ]),
            ]);
    }
}
