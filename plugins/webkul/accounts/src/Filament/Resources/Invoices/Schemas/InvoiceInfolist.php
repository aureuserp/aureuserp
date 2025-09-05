<?php

namespace Webkul\Account\Filament\Resources\Invoices\Schemas;

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

class InvoiceInfolist
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
                Section::make(__('accounts::filament/resources/invoice.infolist.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make()
                            ->schema([
                                TextEntry::make('name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.customer-invoice'))
                                    ->icon('heroicon-o-document')
                                    ->weight('bold')
                                    ->size(TextSize::Large),
                            ])->columns(2),
                        Grid::make()
                            ->schema([
                                TextEntry::make('partner.name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.customer'))
                                    ->visible(fn ($record) => $record->partner_id !== null)
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('invoice_partner_display_name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.customer'))
                                    ->visible(fn ($record) => $record->partner_id === null)
                                    ->icon('heroicon-o-user'),
                                TextEntry::make('invoice_date')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.invoice-date'))
                                    ->icon('heroicon-o-calendar')
                                    ->date(),
                                TextEntry::make('invoice_date_due')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.due-date'))
                                    ->icon('heroicon-o-clock')
                                    ->hidden(fn ($record) => $record->invoice_payment_term_id !== null)
                                    ->date(),
                                TextEntry::make('invoicePaymentTerm.name')
                                    ->placeholder('-')
                                    ->label(__('accounts::filament/resources/invoice.infolist.section.general.entries.payment-term'))
                                    ->hidden(fn ($record) => $record->invoice_payment_term_id === null)
                                    ->icon('heroicon-o-calendar-days'),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.product'))
                                            ->icon('heroicon-o-cube'),
                                        TextEntry::make('quantity')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.quantity'))
                                            ->icon('heroicon-o-hashtag'),
                                        TextEntry::make('uom.name')
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.unit'))
                                            ->icon('heroicon-o-scale'),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.unit-price'))
                                            ->icon('heroicon-o-currency-dollar')
                                            ->money(fn ($record) => $record->currency->code),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.discount-percentage'))
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
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.taxes'))
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/invoice.infolist.tabs.invoice-lines.repeater.products.entries.sub-total'))
                                            ->icon('heroicon-o-calculator')
                                            ->money(fn ($record) => $record->currency->code),
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
                        Tab::make(__('accounts::filament/resources/invoice.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.title'))
                                    ->icon('heroicon-o-document')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('reference')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.customer-reference'))
                                                    ->icon('heroicon-o-hashtag'),
                                                TextEntry::make('invoiceUser.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.sales-person'))
                                                    ->icon('heroicon-o-user'),
                                                TextEntry::make('partnerBank.account_number')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.recipient-bank'))
                                                    ->icon('heroicon-o-building-library'),
                                                TextEntry::make('payment_reference')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.payment-reference'))
                                                    ->icon('heroicon-o-identification'),
                                                TextEntry::make('delivery_date')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.invoice.entries.delivery-date'))
                                                    ->icon('heroicon-o-truck')
                                                    ->date(),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.title'))
                                    ->icon('heroicon-o-calculator')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('invoiceIncoterm.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.incoterm'))
                                                    ->icon('heroicon-o-globe-alt'),
                                                TextEntry::make('incoterm_location')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.incoterm-location'))
                                                    ->icon('heroicon-o-map-pin'),
                                                TextEntry::make('paymentMethodLine.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.payment-method'))
                                                    ->icon('heroicon-o-credit-card'),
                                                IconEntry::make('auto_post')
                                                    ->boolean()
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.auto-post'))
                                                    ->icon('heroicon-o-arrow-path'),
                                                IconEntry::make('checked')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.accounting.fieldset.checked'))
                                                    ->icon('heroicon-o-check-circle')
                                                    ->boolean(),
                                            ])->columns(2),
                                    ]),
                                Section::make(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.marketing.title'))
                                    ->icon('heroicon-o-megaphone')
                                    ->schema([
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('campaign.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.marketing.entries.campaign'))
                                                    ->icon('heroicon-o-presentation-chart-line'),
                                                TextEntry::make('medium.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.marketing.entries.medium'))
                                                    ->icon('heroicon-o-device-phone-mobile'),
                                                TextEntry::make('source.name')
                                                    ->placeholder('-')
                                                    ->label(__('accounts::filament/resources/invoice.infolist.tabs.other-information.fieldset.marketing.entries.source'))
                                                    ->icon('heroicon-o-link'),
                                            ])->columns(2),
                                    ]),
                            ]),
                        Tab::make(__('accounts::filament/resources/invoice.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('narration')
                                    ->html()
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ]);
    }
}
