<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Payment;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;

class PaymentInfolist
{
    public static function configure(Schema $schema, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Grid::make()
                    ->schema([
                        InfolistProgressStepper::make('state')
                            ->hiddenLabel()
                            ->inline()
                            ->options(function ($record) {
                                $options = PaymentStatus::options();

                                if (
                                    $record
                                    && $record->state != PaymentStatus::NOT_PAID
                                ) {
                                    unset($options[PaymentStatus::NOT_PAID->value]);
                                }

                                if (
                                    $record
                                    && $record->state != PaymentStatus::CANCELED
                                ) {
                                    unset($options[PaymentStatus::CANCELED->value]);
                                }

                                if (
                                    $record
                                    && $record->state != PaymentStatus::REJECTED
                                ) {
                                    unset($options[PaymentStatus::REJECTED->value]);
                                }

                                if ($record == null) {
                                    unset($options[PaymentStatus::CANCELED->value]);
                                }

                                return $options;
                            })
                            ->columnSpan('full'),
                    ])->columns(2),

                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.payment-type'))
                                    ->badge(),
                                TextEntry::make('partner.name')
                                    ->label(
                                        fn ($record) => $record->payment_type === PaymentType::RECEIVE
                                            ? __('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer')
                                            : __('accounts::filament/resources/payment.infolist.sections.payment-information.entries.vendor')
                                    )
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),
                                TextEntry::make('amount')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.amount'))
                                    ->money(fn (Payment $record) => $record->currency->name, true)
                                    ->placeholder('—'),
                                TextEntry::make('date')
                                    ->icon('heroicon-o-calendar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.date'))
                                    ->placeholder('—')
                                    ->date(),
                                TextEntry::make('memo')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.memo'))
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('—'),
                            ])
                            ->columns(1),

                        Group::make()
                            ->schema([
                                TextEntry::make('journal.name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.journal'))
                                    ->icon('heroicon-o-building-library')
                                    ->placeholder('—'),
                                TextEntry::make('paymentMethodLine.display_name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.payment-method'))
                                    ->icon('heroicon-o-credit-card')
                                    ->placeholder('—'),
                                TextEntry::make('partnerBank.account_number')
                                    ->label(
                                        fn ($record) => $record->payment_type === PaymentType::RECEIVE
                                            ? __('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer-bank-account')
                                            : __('accounts::filament/resources/payment.infolist.sections.payment-information.entries.vendor-bank-account')
                                    )
                                    ->icon('heroicon-o-building-library')
                                    ->placeholder('—'),
                            ])
                            ->columns(1),

                        ...$customInfolistEntries,
                    ])
                    ->columns(2),
            ]);
    }
}
