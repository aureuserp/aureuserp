<?php

namespace Webkul\Account\Filament\Resources\Payments\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;

class PaymentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make('state')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        PaymentStatus::DRAFT->value      => 'gray',
                                        PaymentStatus::IN_PROCESS->value => 'warning',
                                        PaymentStatus::PAID->value       => 'success',
                                        PaymentStatus::CANCELED->value   => 'danger',
                                        default                          => 'gray',
                                    })
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.state'))
                                    ->formatStateUsing(fn (string $state): string => PaymentStatus::options()[$state]),
                                TextEntry::make('payment_type')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.payment-type'))
                                    ->badge()
                                    ->icon(fn (string $state): string => PaymentType::from($state)->getIcon())
                                    ->color(fn (string $state): string => PaymentType::from($state)->getColor())
                                    ->formatStateUsing(fn (string $state): string => PaymentType::from($state)->getLabel()),
                                TextEntry::make('partnerBank.account_number')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer-bank-account'))
                                    ->icon('heroicon-o-building-library')
                                    ->placeholder('—'),
                                TextEntry::make('partner.name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-information.entries.customer'))
                                    ->icon('heroicon-o-user')
                                    ->placeholder('—'),
                                TextEntry::make('paymentMethodLine.name')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-method.entries.payment-method'))
                                    ->icon('heroicon-o-credit-card')
                                    ->placeholder('—'),
                                TextEntry::make('amount')
                                    ->icon('heroicon-o-currency-dollar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-details.entries.amount'))
                                    ->placeholder('—'),
                                TextEntry::make('date')
                                    ->icon('heroicon-o-calendar')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-details.entries.date'))
                                    ->placeholder('—')
                                    ->date(),
                                TextEntry::make('memo')
                                    ->label(__('accounts::filament/resources/payment.infolist.sections.payment-details.entries.memo'))
                                    ->icon('heroicon-o-document-text')
                                    ->placeholder('—'),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
