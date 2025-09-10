<?php

namespace Webkul\Security\Filament\Resources\Users\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;
use Webkul\Security\Enums\PermissionType;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('partner.avatar')
                    ->size(50)
                    ->label(__('security::filament/resources/user.table.columns.avatar')),
                TextColumn::make('name')
                    ->label(__('security::filament/resources/user.table.columns.name'))
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                TextColumn::make('email')
                    ->label(__('security::filament/resources/user.table.columns.email'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('teams.name')
                    ->label(__('security::filament/resources/user.table.columns.teams'))
                    ->badge(),
                TextColumn::make('roles.name')
                    ->sortable()
                    ->label(__('security::filament/resources/user.table.columns.role')),
                TextColumn::make('resource_permission')
                    ->label(__('security::filament/resources/user.table.columns.resource-permission'))
                    ->formatStateUsing(fn ($state) => PermissionType::options()[$state] ?? $state)
                    ->sortable(),
                TextColumn::make('defaultCompany.name')
                    ->label(__('security::filament/resources/user.table.columns.default-company'))
                    ->sortable(),
                TextColumn::make('allowedCompanies.name')
                    ->label(__('security::filament/resources/user.table.columns.allowed-company'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(__('security::filament/resources/user.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('security::filament/resources/user.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('resource_permission')
                    ->label(__('security::filament/resources/user.table.filters.resource-permission'))
                    ->searchable()
                    ->options(PermissionType::options())
                    ->preload(),
                SelectFilter::make('default_company')
                    ->relationship('defaultCompany', 'name')
                    ->label(__('security::filament/resources/user.table.filters.default-company'))
                    ->searchable()
                    ->preload(),
                SelectFilter::make('allowed_companies')
                    ->relationship('allowedCompanies', 'name')
                    ->label(__('security::filament/resources/user.table.filters.allowed-companies'))
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('teams')
                    ->relationship('teams', 'name')
                    ->label(__('security::filament/resources/user.table.filters.teams'))
                    ->options(fn (): array => Role::query()->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->preload(),
                SelectFilter::make('roles')
                    ->label(__('security::filament/resources/user.table.filters.roles'))
                    ->relationship('roles', 'name')
                    ->options(fn (): array => Role::query()->pluck('name', 'id')->all())
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn ($record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn ($record) => $record->trashed())
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.actions.edit.notification.title'))
                                ->body(__('security::filament/resources/user.table.actions.edit.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.actions.delete.notification.title'))
                                ->body(__('security::filament/resources/user.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.actions.restore.notification.title'))
                                ->body(__('security::filament/resources/user.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.bulk-actions.delete.notification.title'))
                                ->body(__('security::filament/resources/user.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.bulk-actions.force-delete.notification.title'))
                                ->body(__('security::filament/resources/user.table.bulk-actions.force-delete.notification.body')),
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('security::filament/resources/user.table.bulk-actions.restore.notification.title'))
                                ->body(__('security::filament/resources/user.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function ($query) {
                $query->with('roles', 'teams', 'defaultCompany', 'allowedCompanies');
            })
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/user.table.empty-state-actions.create.notification.title'))
                            ->body(__('security::filament/resources/user.table.empty-state-actions.create.notification.body')),
                    ),
            ]);
    }
}
