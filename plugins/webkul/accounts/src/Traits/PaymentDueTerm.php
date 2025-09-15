<?php

namespace Webkul\Account\Traits;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DueTermValue;

trait PaymentDueTerm
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('value')
                    ->options(DueTermValue::class)
                    ->label(__('accounts::traits/payment-due-term.form.value'))
                    ->required(),
                TextInput::make('value_amount')
                    ->label(__('accounts::traits/payment-due-term.form.due'))
                    ->default(100)
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999),
                Select::make('delay_type')
                    ->options(DelayType::class)
                    ->label(__('accounts::traits/payment-due-term.form.delay-type'))
                    ->required(),
                TextInput::make('days_next_month')
                    ->default(10)
                    ->label(__('accounts::traits/payment-due-term.form.days-on-the-next-month')),
                TextInput::make('nb_days')
                    ->default(0)
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->label(__('accounts::traits/payment-due-term.form.days')),
                Select::make('payment_id')
                    ->relationship('paymentTerm', 'name')
                    ->label(__('accounts::traits/payment-due-term.form.payment-term'))
                    ->searchable()
                    ->preload(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('value_amount')
                    ->label(__('accounts::traits/payment-due-term.table.columns.due'))
                    ->sortable(),
                TextColumn::make('value')
                    ->label(__('accounts::traits/payment-due-term.table.columns.value'))
                    ->formatStateUsing(fn ($state) => DueTermValue::options()[$state])
                    ->sortable(),
                TextColumn::make('value_amount')
                    ->label(__('accounts::traits/payment-due-term.table.columns.value-amount'))
                    ->sortable(),
                TextColumn::make('nb_days')
                    ->label(__('accounts::traits/payment-due-term.table.columns.after'))
                    ->sortable(),
                TextColumn::make('delay_type')
                    ->formatStateUsing(fn ($state) => DelayType::options()[$state])
                    ->label(__('accounts::traits/payment-due-term.table.columns.delay-type'))
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::traits/payment-due-term.table.actions.edit.notification.title'))
                            ->body(__('accounts::traits/payment-due-term.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::traits/payment-due-term.table.actions.delete.notification.title'))
                            ->body(__('accounts::traits/payment-due-term.table.actions.delete.notification.body'))
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::traits/payment-due-term.table.actions.delete.notification.title'))
                            ->body(__('accounts::traits/payment-due-term.table.actions.delete.notification.body'))
                    )
                    ->icon('heroicon-o-plus-circle'),
            ]);
    }
}
