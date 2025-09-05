<?php

namespace Webkul\Account\Filament\Resources\Bills\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Account\Livewire\InvoiceSummary;
use Webkul\Invoice\Settings\ProductSettings;

class BillInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('payment_state')
                            ->badge(),
                    ])
                    ->compact(),
                Section::make(__('accounts::filament/resources/bill.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor-invoice'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),
                        Grid::make()
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor'))
                                    ->visible(fn ($record) => $record->partner_id !== null)
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('invoice_partner_display_name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor'))
                                    ->visible(fn ($record) => $record->partner_id === null)
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('invoice_date')
                                    ->date()
                                    ->icon('heroicon-o-calendar')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.bill-date')),
                                TextEntry::make('reference')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.bill-reference')),
                                TextEntry::make('date')
                                    ->icon('heroicon-o-calendar')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.accounting-date')),
                                TextEntry::make('payment_reference')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.payment-reference')),
                                TextEntry::make('partnerBank.account_number')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.recipient-bank')),
                                TextEntry::make('invoice_date_due')
                                    ->icon('heroicon-o-clock')
                                    ->placeholder('-')
                                    ->date()
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.due-date')),
                                TextEntry::make('invoicePaymentTerm.name')
                                    ->placeholder('-')
                                    ->icon('heroicon-o-calendar-days')
                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.payment-term')),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        TextEntry::make('quantity')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.unit'))
                                            ->icon('heroicon-o-scale'),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.unit-price'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->money(fn ($record) => $record->currency->name),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.discount-percentage'))
                                            ->icon('heroicon-o-tag')
                                            ->suffix('%'),
                                        TextEntry::make('taxes.name')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->icon('heroicon-o-receipt-percent')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.taxes'))
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.sub-total'))
                                            ->icon('heroicon-o-calculator')
                                            ->money(fn ($record) => $record->currency->name),
                                    ])->columns(5),
                                Livewire::make(InvoiceSummary::class, function ($record) {
                                    return [
                                        'currency'  => $record->currency,
                                        'amountTax' => $record->amount_tax ?? 0,
                                        'products'  => $record->lines->map(function ($item) {
                                            return [
                                                ...$item->toArray(),
                                                'taxes' => $item->taxes->pluck('id')->toArray() ?? [],
                                            ];
                                        })->toArray(),
                                    ];
                                }),
                            ]),
                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.title'))
                                    ->icon('heroicon-o-calculator')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('invoiceIncoterm.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.incoterm'))
                                                    ->icon('heroicon-o-globe-alt'),
                                                TextEntry::make('incoterm_location')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.incoterm-location'))
                                                    ->icon('heroicon-o-map-pin'),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.secured.title'))
                                    ->icon('heroicon-o-shield-check')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('paymentMethodLine.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.secured.entries.payment-method'))
                                                    ->icon('heroicon-o-credit-card'),
                                                IconEntry::make('auto_post')
                                                    ->boolean()
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.secured.entries.auto-post')),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.additional-information.title'))
                                    ->icon('heroicon-o-puzzle-piece')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('company.name')
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-building-office')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.additional-information.entries.company')),
                                                TextEntry::make('currency.name')
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.additional-information.entries.currency')),
                                            ])->columns(2),
                                    ]),
                            ]),
                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('narration')
                                    ->html()
                                    ->hiddenLabel(),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }
}
