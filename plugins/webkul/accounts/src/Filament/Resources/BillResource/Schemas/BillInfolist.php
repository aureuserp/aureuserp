<?php

namespace Webkul\Account\Filament\Resources\BillResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Webkul\Account\Enums\MoveState;
use Webkul\Field\Filament\Infolists\Components\ProgressStepper as InfolistProgressStepper;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Support\Filament\Infolists\Components\RepeatableEntry;
use Webkul\Support\Filament\Infolists\Components\Repeater\TableColumn as InfolistTableColumn;

class BillInfolist
{
    public static function configure(Schema $schema, string $resource, array $customInfolistEntries = []): Schema
    {
        return $schema
            ->components([
                InfolistProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = MoveState::options();

                        if ($record?->state !== MoveState::CANCEL) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(MoveState::DRAFT->value)
                    ->columnSpan('full'),

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
                                Group::make()
                                    ->schema([
                                        TextEntry::make('partner.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor'))
                                            ->visible(fn ($record) => $record->partner_id !== null)
                                            ->icon('heroicon-o-user'),
                                        TextEntry::make('reference')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.bill-reference')),
                                    ])
                                    ->columns(1),
                                Group::make()
                                    ->schema([
                                        TextEntry::make('invoice_partner_display_name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.vendor'))
                                            ->visible(fn ($record) => $record->partner_id === null)
                                            ->icon('heroicon-o-user'),
                                        TextEntry::make('invoice_date')
                                            ->date()
                                            ->icon('heroicon-o-calendar')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.bill-date')),
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
                                        Grid::make()
                                            ->schema([
                                                TextEntry::make('journal.name')
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.journal')),
                                                TextEntry::make('currency.name')
                                                    ->placeholder('-')
                                                    ->icon('heroicon-o-arrow-path')
                                                    ->label(__('accounts::filament/resources/bill.infolist.section.general.entries.currency')),
                                            ]),
                                    ])
                                    ->columns(1),
                            ])
                            ->columns(2),
                    ]),

                Tabs::make()
                    ->columnSpan('full')
                    ->tabs([
                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('invoiceLines')
                                    ->columnManager()
                                    ->columnManagerColumns(2)
                                    ->live()
                                    ->hiddenLabel()
                                    ->table([
                                        InfolistTableColumn::make('name')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.product')),
                                        InfolistTableColumn::make('quantity')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.quantity')),
                                        InfolistTableColumn::make('uom')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.unit')),
                                        InfolistTableColumn::make('price_unit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.unit-price')),
                                        InfolistTableColumn::make('discount')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.discount-percentage')),
                                        InfolistTableColumn::make('taxes')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.taxes')),
                                        InfolistTableColumn::make('price_subtotal')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.invoice-lines.repeater.products.entries.sub-total')),
                                    ])
                                    ->schema([
                                        TextEntry::make('name')
                                            ->placeholder('-'),
                                        TextEntry::make('quantity')
                                            ->placeholder('-'),
                                        TextEntry::make('uom')
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-')
                                            ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                                        TextEntry::make('price_unit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency->name),
                                        TextEntry::make('discount')
                                            ->placeholder('-')
                                            ->suffix('%'),
                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->formatStateUsing(fn ($state) => $state['name'])
                                            ->placeholder('-')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('price_subtotal')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency->name),
                                    ]),

                                Livewire::make($resource::getSummaryComponent(), function ($record) {
                                    $rounding = $record->roundingLines->sum('balance');

                                    return [
                                        'currency'   => $record->currency,
                                        'subtotal'   => $record->amount_untaxed ?? 0,
                                        'totalTax'   => $record->amount_tax ?? 0,
                                        'amountTax'  => $record->amount_tax ?? 0,
                                        'grandTotal' => $record->amount_total ?? 0,
                                        'rounding'   => $rounding,
                                    ];
                                }),
                            ]),

                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.journal-items.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                RepeatableEntry::make('lines')
                                    ->hiddenLabel()
                                    ->columnManager()
                                    ->live()
                                    ->table([
                                        InfolistTableColumn::make('account')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.account')),
                                        InfolistTableColumn::make('partner')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.partner')),
                                        InfolistTableColumn::make('name')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.label')),
                                        InfolistTableColumn::make('currency')
                                            ->alignCenter()
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.currency')),
                                        InfolistTableColumn::make('date_maturity')
                                            ->alignCenter()
                                            ->toggleable(isToggledHiddenByDefault: true)
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.due-date')),
                                        InfolistTableColumn::make('taxes')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.taxes')),
                                        InfolistTableColumn::make('debit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.debit')),
                                        InfolistTableColumn::make('credit')
                                            ->alignCenter()
                                            ->toggleable()
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.journal-items.repeater.entries.credit')),
                                    ])
                                    ->schema([
                                        TextEntry::make('account')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-'),
                                        TextEntry::make('partner')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state ? ($state['name'] ?? '-') : '-'),
                                        TextEntry::make('name')
                                            ->placeholder('-'),
                                        TextEntry::make('currency')
                                            ->placeholder('-')
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-'),
                                        TextEntry::make('date_maturity')
                                            ->placeholder('-')
                                            ->date(),
                                        TextEntry::make('taxes')
                                            ->badge()
                                            ->state(function ($record): array {
                                                return $record->taxes->map(fn ($tax) => [
                                                    'name' => $tax->name,
                                                ])->toArray();
                                            })
                                            ->formatStateUsing(fn ($state) => $state['name'] ?? '-')
                                            ->placeholder('-')
                                            ->weight(FontWeight::Bold),
                                        TextEntry::make('debit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name),
                                        TextEntry::make('credit')
                                            ->placeholder('-')
                                            ->money(fn ($record) => $record->currency?->name),
                                    ])->columns(5),
                            ]),

                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.title'))
                                    ->schema([
                                        TextEntry::make('company.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.company')),
                                        TextEntry::make('invoiceIncoterm.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.incoterm')),
                                        TextEntry::make('incoterm_location')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.incoterm-location')),
                                        TextEntry::make('fiscalPosition.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.fiscal-position')),
                                        TextEntry::make('cashRounding.name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.cash-rounding')),
                                        TextEntry::make('paymentMethodLine.display_name')
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.payment-method')),
                                        IconEntry::make('checked')
                                            ->boolean()
                                            ->placeholder('-')
                                            ->label(__('accounts::filament/resources/bill.infolist.tabs.other-information.fieldset.accounting.entries.checked')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(2),

                        Tab::make(__('accounts::filament/resources/bill.infolist.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                TextEntry::make('narration')
                                    ->html()
                                    ->hiddenLabel(),
                            ]),
                    ]),

                Section::make()
                    ->schema($customInfolistEntries)
                    ->columns(2),
            ])
            ->columns(1);
    }
}
