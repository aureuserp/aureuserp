<?php

namespace Webkul\Account\Filament\Resources\Taxes\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TaxScope;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Tax;

class TaxesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/tax.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/tax.table.columns.company'))
                    ->sortable(),
                TextColumn::make('taxGroup.name')
                    ->label(__('Tax Group'))
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-group'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('country.name')
                    ->label(__('accounts::filament/resources/tax.table.columns.country'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('type_tax_use')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-type'))
                    ->formatStateUsing(fn ($state) => TypeTaxUse::options()[$state])
                    ->sortable(),
                TextColumn::make('tax_scope')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-scope'))
                    ->formatStateUsing(fn ($state) => TaxScope::options()[$state])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_type')
                    ->label(__('accounts::filament/resources/tax.table.columns.amount-type'))
                    ->formatStateUsing(fn ($state) => AmountType::options()[$state])
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_label')
                    ->label(__('accounts::filament/resources/tax.table.columns.invoice-label'))
                    ->sortable(),
                TextColumn::make('tax_exigibility')
                    ->label(__('accounts::filament/resources/tax.table.columns.tax-exigibility'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('price_include_override')
                    ->label(__('accounts::filament/resources/tax.table.columns.price-include-override'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount')
                    ->label(__('accounts::filament/resources/tax.table.columns.amount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.status'))
                    ->sortable(),
                IconColumn::make('include_base_amount')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.include-base-amount'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('is_base_affected')
                    ->boolean()
                    ->label(__('accounts::filament/resources/tax.table.columns.is-base-affected'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Group::make('name')
                    ->label(__('accounts::filament/resources/tax.table.groups.name'))
                    ->collapsible(),
                Group::make('company.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.company'))
                    ->collapsible(),
                Group::make('taxGroup.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.tax-group'))
                    ->collapsible(),
                Group::make('country.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.country'))
                    ->collapsible(),
                Group::make('createdBy.name')
                    ->label(__('accounts::filament/resources/tax.table.groups.created-by'))
                    ->collapsible(),
                Group::make('type_tax_use')
                    ->label(__('accounts::filament/resources/tax.table.groups.type-tax-use'))
                    ->collapsible(),
                Group::make('tax_scope')
                    ->label(__('accounts::filament/resources/tax.table.groups.tax-scope'))
                    ->collapsible(),
                Group::make('amount_type')
                    ->label(__('accounts::filament/resources/tax.table.groups.amount-type'))
                    ->collapsible(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    ViewAction::make(),
                    DeleteAction::make()
                        ->action(function (Tax $record) {
                            try {
                                $record->delete();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax.table.actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/tax.table.actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/tax.table.actions.delete.notification.success.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->action(function (Collection $records) {
                            try {
                                $records->each(fn (Model $record) => $record->delete());
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.error.title'))
                                    ->body(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.error.body'))
                                    ->send();
                            }
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.success.title'))
                                ->body(__('accounts::filament/resources/tax.table.bulk-actions.delete.notification.success.body'))
                        ),
                ]),
            ])
            ->reorderable('sort', 'desc');
    }
}
