<?php

namespace Webkul\Account\Traits;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Account\Enums;

trait PaymentDueTerm
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('value')
                    ->options(Enums\DueTermValue::class)
                    ->label(__('accounts::traits/payment-due-term.form.value'))
                    ->required(),
                Forms\Components\TextInput::make('value_amount')
                    ->label(__('accounts::traits/payment-due-term.form.due'))
                    ->default(100)
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999),
                Forms\Components\Select::make('delay_type')
                    ->options(Enums\DelayType::class)
                    ->label(__('accounts::traits/payment-due-term.form.delay-type'))
                    ->required(),
                Forms\Components\TextInput::make('days_next_month')
                    ->default(10)
                    ->label(__('accounts::traits/payment-due-term.form.days-on-the-next-month')),
                Forms\Components\TextInput::make('nb_days')
                    ->default(0)
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->label(__('accounts::traits/payment-due-term.form.days')),
                Forms\Components\Select::make('payment_id')
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
                Tables\Columns\TextColumn::make('value_amount')
                    ->label(__('accounts::traits/payment-due-term.table.columns.due'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label(__('accounts::traits/payment-due-term.table.columns.value'))
                    ->formatStateUsing(fn ($state) => Enums\DueTermValue::options()[$state])
                    ->sortable(),
                Tables\Columns\TextColumn::make('value_amount')
                    ->label(__('accounts::traits/payment-due-term.table.columns.value-amount'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('nb_days')
                    ->label(__('accounts::traits/payment-due-term.table.columns.after'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('delay_type')
                    ->formatStateUsing(fn ($state) => Enums\DelayType::options()[$state])
                    ->label(__('accounts::traits/payment-due-term.table.columns.delay-type'))
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::traits/payment-due-term.table.actions.edit.notification.title'))
                            ->body(__('accounts::traits/payment-due-term.table.actions.edit.notification.body'))
                    ),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::traits/payment-due-term.table.actions.delete.notification.title'))
                            ->body(__('accounts::traits/payment-due-term.table.actions.delete.notification.body'))
                    ),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
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
