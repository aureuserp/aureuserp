<?php

namespace Webkul\Support\Filament\Resources\CompanyResource\Tables;

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
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Webkul\Security\Settings\UserSettings;
use Webkul\Support\Enums\CompanyStatus;
use Webkul\Support\Models\Company;

class CompaniesTable
{
    public static function configure(Table $table, array $customColumns = [], array $customFilters = []): Table
    {
        return $table
            ->reorderableColumns()
            ->columns(array_merge([
                ImageColumn::make('partner.avatar')
                    ->circular()
                    ->imageSize(50)
                    ->label(__('support::filament/resources/company.table.columns.logo')),
                TextColumn::make('name')
                    ->label(__('support::filament/resources/company.table.columns.company-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('branches.name')
                    ->label(__('support::filament/resources/company.table.columns.branches'))
                    ->placeholder('-')
                    ->badge()
                    ->searchable(),
                TextColumn::make('email')
                    ->label(__('support::filament/resources/company.table.columns.email'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('city')
                    ->label(__('support::filament/resources/company.table.columns.city'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('country.name')
                    ->label(__('support::filament/resources/company.table.columns.country'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.full_name')
                    ->label(__('support::filament/resources/company.table.columns.currency'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('is_active')
                    ->sortable()
                    ->label(__('support::filament/resources/company.table.columns.status'))
                    ->boolean(),
                TextColumn::make('creator.name')
                    ->label(__('support::filament/resources/company.table.columns.created-by'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label(__('support::filament/resources/company.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('support::filament/resources/company.table.columns.updated-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ], $customColumns))
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('support::filament/resources/company.table.groups.company-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('city')
                    ->label(__('support::filament/resources/company.table.groups.city'))
                    ->collapsible(),
                Tables\Grouping\Group::make('country.name')
                    ->label(__('support::filament/resources/company.table.groups.country'))
                    ->collapsible(),
                Tables\Grouping\Group::make('state.name')
                    ->label(__('support::filament/resources/company.table.groups.state'))
                    ->collapsible(),
                Tables\Grouping\Group::make('email')
                    ->label(__('support::filament/resources/company.table.groups.email'))
                    ->collapsible(),
                Tables\Grouping\Group::make('phone')
                    ->label(__('support::filament/resources/company.table.groups.phone'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency_id')
                    ->label(__('support::filament/resources/company.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('creator.name')
                    ->label(__('support::filament/resources/company.table.groups.created-by'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('support::filament/resources/company.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('support::filament/resources/company.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filters(array_merge([
                SelectFilter::make('is_active')
                    ->label(__('support::filament/resources/company.table.filters.status'))
                    ->options(CompanyStatus::class),
                SelectFilter::make('country_id')
                    ->label(__('support::filament/resources/company.table.filters.country'))
                    ->multiple()
                    ->relationship(name: 'country', titleAttribute: 'name'),
            ], $customFilters))
            ->filtersFormColumns(2)
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn ($record, $livewire = null) => ! $record->trashed() && ! static::isArchivedTab($livewire))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('support::filament/resources/company.table.actions.edit.notification.title')))
                                ->body(__('support::filament/resources/company.table.actions.edit.notification.body')),
                        ),
                    DeleteAction::make()
                        ->visible(fn ($record, $livewire = null) => ! $record->trashed() && ! static::isArchivedTab($livewire))
                        ->before(fn ($record, $action) => static::cancelIfDefaultCompany($record->id, $action))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('support::filament/resources/company.table.actions.delete.notification.title')))
                                ->body(__('support::filament/resources/company.table.actions.delete.notification.body')),
                        ),
                    RestoreAction::make()
                        ->visible(fn ($record, $livewire = null) => $record->trashed() && static::isArchivedTab($livewire))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('support::filament/resources/company.table.actions.restore.notification.title')))
                                ->body(__('support::filament/resources/company.table.actions.restore.notification.body')),
                        ),
                    ForceDeleteAction::make()
                        ->visible(fn ($record, $livewire = null) => $record->trashed() && static::isArchivedTab($livewire))
                        ->before(fn ($record, $action) => static::cancelIfDefaultCompany($record->id, $action))
                        ->action(function (ForceDeleteAction $action, Company $record) {
                            try {
                                $record->forceDelete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('support::filament/resources/company.table.actions.force-delete.notification.error.title'))
                                    ->body(__('support::filament/resources/company.table.actions.force-delete.notification.error.body'))
                                    ->send();

                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('support::filament/resources/company.table.actions.force-delete.notification.success.title')))
                                ->body(__('support::filament/resources/company.table.actions.force-delete.notification.success.body')),
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn ($livewire = null) => ! static::isArchivedTab($livewire))
                        ->before(fn ($records, $action) => static::cancelIfDefaultCompany($records->pluck('id')->all(), $action))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('support::filament/resources/company.table.bulk-actions.delete.notification.title')))
                                ->body(__('support::filament/resources/company.table.bulk-actions.delete.notification.body')),
                        ),
                    ForceDeleteBulkAction::make()
                        ->action(function (ForceDeleteBulkAction $action, Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->forceDelete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('support::filament/resources/company.table.bulk-actions.force-delete.notification.error.title'))
                                    ->body(__('support::filament/resources/company.table.bulk-actions.force-delete.notification.error.body'))
                                    ->send();

                                $action->cancel();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('support::filament/resources/company.table.bulk-actions.force-delete.notification.title')))
                                ->body(__('support::filament/resources/company.table.bulk-actions.force-delete.notification.body')),
                        ),
                    RestoreBulkAction::make()
                        ->visible(fn ($livewire = null) => static::isArchivedTab($livewire))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title((__('support::filament/resources/company.table.bulk-actions.restore.notification.title')))
                                ->body(__('support::filament/resources/company.table.bulk-actions.restore.notification.body')),
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => true
            )
            ->reorderable('sort');
    }

    private static function isArchivedTab($livewire = null): bool
    {
        if (! is_object($livewire) || ! property_exists($livewire, 'activeTab')) {
            return false;
        }

        return $livewire->activeTab === 'archived';
    }

    private static function cancelIfDefaultCompany(int|array $ids, $action): void
    {
        $ids = (array) $ids;

        if (! in_array(settings(UserSettings::class)->default_company_id, $ids)) {
            return;
        }

        Notification::make()
            ->warning()
            ->title(__('support::filament/resources/company.table.actions.delete.notification.default-company.title'))
            ->body(__('support::filament/resources/company.table.actions.delete.notification.default-company.body'))
            ->send();

        $action->cancel();
    }
}
