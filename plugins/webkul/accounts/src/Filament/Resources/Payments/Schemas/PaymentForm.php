<?php

namespace Webkul\Account\Filament\Resources\Payments\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make()
                    ->schema([
                        ProgressStepper::make('state')
                            ->hiddenLabel()
                            ->inline()
                            ->options(PaymentStatus::class)
                            ->default(PaymentStatus::DRAFT->value)
                            ->columnSpan('full')
                            ->disabled()
                            ->live()
                            ->reactive(),
                    ])->columns(2),
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                ToggleButtons::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-type'))
                                    ->options(PaymentType::class)
                                    ->default(PaymentType::SEND->value)
                                    ->inline(true),
                                Select::make('partner_bank_id')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.customer-bank-account'))
                                    ->relationship(
                                        'partnerBank',
                                        'account_number',
                                        modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->account_number.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                Select::make('partner_id')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.customer'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload(),
                                Select::make('payment_method_line_id')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.payment-method'))
                                    ->relationship(
                                        'paymentMethodLine',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload(),
                                TextInput::make('amount')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.amount'))
                                    ->default(0)
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required(),
                                DatePicker::make('date')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.date'))
                                    ->native(false)
                                    ->default(now())
                                    ->required(),
                                TextInput::make('memo')
                                    ->label(__('accounts::filament/resources/payment.form.sections.fields.memo'))
                                    ->maxLength(255),
                            ])->columns(2),
                    ]),
            ])
            ->columns(1);
    }
}
