<?php

namespace Webkul\Account\Filament\Resources\AccountTags\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AccountTagsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.color'))
                    ->searchable(),
                TextColumn::make('country.name')
                    ->numeric()
                    ->maxValue(99999999999)
                    ->label(__('accounts::filament/resources/account-tag.table.columns.country'))
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.created-by'))
                    ->sortable(),
                TextColumn::make('applicability')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.applicability'))
                    ->searchable(),
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.name'))
                    ->searchable(),
                IconColumn::make('tax_negate')
                    ->label(__('accounts::filament/resources/account-tag.table.columns.tax-negate'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/account-tag.table.columns.created-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->label(__('accounts::filament/resources/account-tag.table.columns.updated-at'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('country.name')
                    ->label(__('accounts::filament/resources/account-tag.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('createdBy.name')
                    ->label(__('accounts::filament/resources/account-tag.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('applicability')
                    ->label(__('accounts::filament/resources/account-tag.table.groups.applicability'))
                    ->collapsible(),
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/account-tag.table.groups.name'))
                    ->collapsible(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title('accounts::filament/clusters/configurations/resources/account-tag.table.actions.edit.notification.title')
                            ->body('accounts::filament/clusters/configurations/resources/account-tag.table.actions.edit.notification.body')
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->title('accounts::filament/clusters/configurations/resources/account-tag.table.actions.delete.notification.title')
                            ->body('accounts::filament/clusters/configurations/resources/account-tag.table.actions.delete.notification.body')
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->title('accounts::filament/clusters/configurations/resources/account-tag.table.bulk-actions.delete.notification.title')
                                ->body('accounts::filament/clusters/configurations/resources/account-tag.table.bulk-actions.delete.notification.body')
                        ),
                ]),
            ]);
    }
}
