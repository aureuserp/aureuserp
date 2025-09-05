<?php

namespace Webkul\Security\Filament\Resources\Companies\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Enums\CompanyStatus;
use Webkul\Security\Filament\Resources\Companies\CompanyResource;
use Webkul\Support\Models\Country;

class CompaniesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns(CompanyResource::mergeCustomTableColumns([
                ImageColumn::make('partner.avatar')
                    ->circular()
                    ->size(50)
                    ->label(__('security::filament/resources/company.table.columns.logo')),
                TextColumn::make('name')
                    ->label(__('security::filament/resources/company.table.columns.company-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('branches.name')
                    ->label(__('security::filament/resources/company.table.columns.branches'))
                    ->placeholder('-')
                    ->badge()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('security::filament/resources/company.table.columns.email'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('city')
                    ->label(__('security::filament/resources/company.table.columns.city'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country.name')
                    ->label(__('security::filament/resources/company.table.columns.country'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.full_name')
                    ->label(__('security::filament/resources/company.table.columns.currency'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('security::filament/resources/company.table.columns.status'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('security::filament/resources/company.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('security::filament/resources/company.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]))
            ->columnToggleFormColumns(2)
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('security::filament/resources/company.table.groups.company-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('city')
                    ->label(__('security::filament/resources/company.table.groups.city'))
                    ->collapsible(),
                Tables\Grouping\Group::make('country.name')
                    ->label(__('security::filament/resources/company.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state.name')
                    ->label(__('security::filament/resources/company.table.groups.state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('email')
                    ->label(__('security::filament/resources/company.table.groups.email'))
                    ->collapsible(),
                Tables\Grouping\Group::make('phone')
                    ->label(__('security::filament/resources/company.table.groups.phone'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency_id')
                    ->label(__('security::filament/resources/company.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('security::filament/resources/company.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('security::filament/resources/company.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('is_active')
                    ->label(__('security::filament/resources/company.table.filters.status'))
                    ->options(CompanyStatus::options()),
                SelectFilter::make('country')
                    ->label(__('security::filament/resources/company.table.filters.country'))
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
                                ->title((__('security::filament/resources/company.table.actions.edit.notification.title')))
                                ->body(__('security::filament/resources/company.table.actions.edit.notification.body')),
                        ),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company.table.actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.bulk-actions.delete.notification.title')))
                                ->body(__('security::filament/resources/company.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.bulk-actions.force-delete.notification.title')))
                                ->body(__('security::filament/resources/company.table.bulk-actions.force-delete.notification.body')),
                        ),
                    RestoreBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('security::filament/resources/company.table.bulk-actions.restore.notification.title')))
                                ->body(__('security::filament/resources/company.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ])->modifyQueryUsing(function (Builder $query) {
                $query
                    ->where('creator_id', Auth::user()->id)
                    ->whereNull('parent_id');
            })
            ->reorderable('sequence');
    }
}
