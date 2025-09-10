<?php

namespace Webkul\Security\Filament\Resources\Companies\RelationManagers\Tables;

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
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Enums\CompanyStatus;
use Webkul\Support\Models\Country;

class BranchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('partner.avatar')
                    ->size(50)
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.logo')),
                TextColumn::make('name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.company-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.email'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('city')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.city'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country.name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.country'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.full_name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.currency'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.status'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnToggleFormColumns(2)
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.company-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('city')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.city'))
                    ->collapsible(),
                Tables\Grouping\Group::make('country.name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state.name')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('email')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.email'))
                    ->collapsible(),
                Tables\Grouping\Group::make('phone')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.phone'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency_id')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function ($livewire, array $data): array {
                        $data['user_id'] = Auth::user()->id;

                        $data['parent_id'] = $livewire->ownerRecord->id;

                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.header-actions.create.notification.title')))
                            ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.header-actions.create.notification.body')),
                    ),
            ])
            ->filters([
                TrashedFilter::make()
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.filters.trashed')),
                SelectFilter::make('is_active')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.filters.status'))
                    ->options(CompanyStatus::options()),
                SelectFilter::make('country')
                    ->label(__('security::filament/resources/company/relation-managers/manage-branch.table.filters.country'))
                    ->multiple()
                    ->options(function () {
                        return Country::pluck('name', 'name');
                    }),
            ])
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.actions.edit.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.actions.edit.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.force-delete.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.force-delete.notification.body')),
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company/relation-managers/manage-branch.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->reorderable('sequence');
    }
}
