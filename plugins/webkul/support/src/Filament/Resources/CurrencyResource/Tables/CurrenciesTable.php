<?php

namespace Webkul\Support\Filament\Resources\CurrencyResource\Tables;

use Closure;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;
use Webkul\Support\Models\Currency;

class CurrenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort(fn ($query) => $query->orderBy('active', 'desc')->orderBy('name', 'asc'))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label(__('support::filament/resources/currency.table.columns.name'))
                    ->sortable(),
                TextColumn::make('symbol')
                    ->label(__('support::filament/resources/currency.table.columns.symbol'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label(__('support::filament/resources/currency.table.columns.full-name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('iso_numeric')
                    ->label(__('support::filament/resources/currency.table.columns.iso-numeric'))
                    ->sortable(),
                TextColumn::make('decimal_places')
                    ->label(__('support::filament/resources/currency.table.columns.decimal-places'))
                    ->sortable(),
                TextColumn::make('rounding')
                    ->label(__('support::filament/resources/currency.table.columns.rounding'))
                    ->money('USD', divideBy: 1)
                    ->sortable(),
                ToggleColumn::make('active')
                    ->label(__('support::filament/resources/currency.table.columns.status'))
                    ->rules(static fn (Currency $record): array => [
                        'boolean',
                        static function (string $attribute, $value, Closure $fail) use ($record): void {
                            if (! $value && $record->isInUse()) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('support::filament/resources/currency.table.actions.deactivate.notification.title'))
                                    ->body(__('support::filament/resources/currency.table.actions.deactivate.notification.body'))
                                    ->send();

                                $fail(__('support::filament/resources/currency.table.actions.deactivate.notification.body'));
                            }
                        },
                    ])
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('support::filament/resources/currency.table.columns.created-at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(__('support::filament/resources/currency.table.columns.updated-at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('support::filament/resources/currency.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('active')
                    ->label(__('support::filament/resources/currency.table.groups.status'))
                    ->collapsible(),
                Tables\Grouping\Group::make('decimal_places')
                    ->label(__('support::filament/resources/currency.table.groups.decimal-places'))
                    ->collapsible(),
            ])
            ->filters([
                TernaryFilter::make('active')
                    ->label(__('support::filament/resources/currency.table.filters.status')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/currency.table.actions.delete.notification.title'))
                                ->body(__('support::filament/resources/currency.table.actions.delete.notification.body')),
                        )
                        ->action(function (Currency $record) {
                            try {
                                $record->delete();

                                Notification::make()
                                    ->success()
                                    ->title(__('support::filament/resources/currency.table.actions.delete.notification.success.title'))
                                    ->body(__('support::filament/resources/currency.table.actions.delete.notification.success.body'))
                                    ->send();
                            } catch (QueryException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title(__('support::filament/resources/currency.table.actions.delete.notification.error.title'))
                                    ->body(__('support::filament/resources/currency.table.actions.delete.notification.error.body'))
                                    ->send();
                            }
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('support::filament/resources/currency.table.bulk-actions.delete.notification.title'))
                                ->body(__('support::filament/resources/currency.table.bulk-actions.delete.notification.body')),
                        ),
                ]),
            ]);
    }
}
