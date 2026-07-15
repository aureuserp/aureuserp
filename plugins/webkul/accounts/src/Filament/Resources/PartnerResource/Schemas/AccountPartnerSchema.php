<?php

namespace Webkul\Account\Filament\Resources\PartnerResource\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\AutoPostBills;
use Webkul\Account\Enums\InvoiceFormat;
use Webkul\Account\Enums\InvoiceSendingMethod;
use Webkul\Account\Enums\PartyIdentificationScheme;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Account;

class AccountPartnerSchema
{
    public static function salesFields(): array
    {
        return [
            Group::make()
                ->schema([
                    Select::make('property_payment_term_id')
                        ->relationship('propertyPaymentTerm', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.sales.fields.payment-terms')),
                    Select::make('property_inbound_payment_method_line_id')
                        ->relationship(
                            'propertyInboundPaymentMethodLine',
                            'name',
                            modifyQueryUsing: fn ($query) => $query->whereHas('paymentMethod', fn ($q) => $q->where('payment_type', PaymentType::RECEIVE)),
                        )
                        ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                        ->preload()
                        ->searchable()
                        ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.sales.fields.payment-method')),
                ])
                ->columns(2),
        ];
    }

    public static function salesPurchaseAppend(): array
    {
        return [
            Fieldset::make(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.purchase.title'))
                ->schema([
                    Group::make()
                        ->schema([
                            Select::make('property_supplier_payment_term_id')
                                ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.purchase.fields.payment-terms'))
                                ->relationship('propertySupplierPaymentTerm', 'name')
                                ->searchable()
                                ->preload(),
                            Select::make('property_outbound_payment_method_line_id')
                                ->relationship(
                                    'propertyOutboundPaymentMethodLine',
                                    'name',
                                    modifyQueryUsing: fn ($query) => $query->whereHas('paymentMethod', fn ($q) => $q->where('payment_type', PaymentType::SEND)),
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                                ->preload()
                                ->searchable()
                                ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.purchase.fields.payment-method')),
                        ])->columns(2),
                ])
                ->columns(1),

            Fieldset::make(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.fiscal-information.title'))
                ->schema([
                    Group::make()
                        ->schema([
                            Select::make('property_account_position_id')
                                ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.fiscal-information.fields.fiscal-position'))
                                ->relationship('propertyAccountPosition', 'name')
                                ->searchable()
                                ->preload(),
                        ])->columns(2),
                ])
                ->columns(1),
        ];
    }

    public static function invoicingTab(): Tab
    {
        return Tab::make(__('accounts::filament/resources/partner.form.tabs.invoicing.title'))
            ->icon('heroicon-o-receipt-percent')
            ->schema([
                Fieldset::make(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.customer-invoices.title'))
                    ->schema([
                        Select::make('invoice_sending_method')
                            ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.customer-invoices.fields.invoice-sending-method'))
                            ->options(InvoiceSendingMethod::class),
                        Select::make('invoice_edi_format_store')
                            ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.customer-invoices.fields.invoice-edi-format-store'))
                            ->live()
                            ->options(InvoiceFormat::class),
                        Group::make()
                            ->schema([
                                Select::make('peppol_eas')
                                    ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.customer-invoices.fields.peppol-eas'))
                                    ->live()
                                    ->visible(fn (Get $get) => $get('invoice_edi_format_store') !== InvoiceFormat::FACTURX_X_CII->value && ! empty($get('invoice_edi_format_store')))
                                    ->options(PartyIdentificationScheme::class),
                                TextInput::make('peppol_endpoint')
                                    ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.customer-invoices.fields.endpoint'))
                                    ->live()
                                    ->visible(fn (Get $get) => $get('invoice_edi_format_store') !== InvoiceFormat::FACTURX_X_CII->value && ! empty($get('invoice_edi_format_store'))),
                            ])->columns(2),
                    ]),

                Fieldset::make(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.accounting-entries.title'))
                    ->schema([
                        Select::make('property_account_receivable_id')
                            ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.accounting-entries.fields.account-receivable'))
                            ->relationship('propertyAccountReceivable', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(Account::where('account_type', AccountType::ASSET_RECEIVABLE)->where('deprecated', false)->first()?->id),
                        Select::make('property_account_payable_id')
                            ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.accounting-entries.fields.account-payable'))
                            ->relationship('propertyAccountPayable', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(Account::where('account_type', AccountType::LIABILITY_PAYABLE)->where('deprecated', false)->first()?->id),
                    ]),

                Fieldset::make(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.automation.title'))
                    ->schema([
                        Select::make('autopost_bills')
                            ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.automation.fields.auto-post-bills'))
                            ->options(AutoPostBills::class),
                        Toggle::make('ignore_abnormal_invoice_amount')
                            ->inline(false)
                            ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.automation.fields.ignore-abnormal-invoice-amount')),
                        Toggle::make('ignore_abnormal_invoice_date')
                            ->inline(false)
                            ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.automation.fields.ignore-abnormal-invoice-date')),
                    ]),
            ]);
    }

    public static function internalNotesTab(): Tab
    {
        return Tab::make(__('accounts::filament/resources/partner.form.tabs.internal-notes.title'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                RichEditor::make('comment')
                    ->hiddenLabel(),
            ]);
    }

    public static function salesEntries(): array
    {
        return [
            Group::make()
                ->schema([
                    TextEntry::make('propertyPaymentTerm.name')
                        ->placeholder('-')
                        ->label(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.sales.entries.payment-terms'))
                        ->icon('heroicon-o-calendar'),
                    TextEntry::make('propertyInboundPaymentMethodLine.name')
                        ->placeholder('-')
                        ->label(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.sales.entries.payment-method'))
                        ->icon('heroicon-o-credit-card'),
                ])
                ->columns(2),
        ];
    }

    public static function salesPurchaseAppendInfolist(): array
    {
        return [
            Fieldset::make(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.purchase.title'))
                ->schema([
                    Group::make()
                        ->schema([
                            TextEntry::make('propertySupplierPaymentTerm.name')
                                ->label(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.purchase.entries.payment-terms'))
                                ->placeholder('-')
                                ->icon('heroicon-o-calendar'),
                            TextEntry::make('propertyOutboundPaymentMethodLine.name')
                                ->placeholder('-')
                                ->label(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.purchase.entries.payment-method'))
                                ->icon('heroicon-o-banknotes'),
                        ])->columns(2),
                ])
                ->columns(1),

            Fieldset::make(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.fiscal-information.title'))
                ->schema([
                    Group::make()
                        ->schema([
                            TextEntry::make('propertyAccountPosition.name')
                                ->label(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.fiscal-information.entries.fiscal-position'))
                                ->placeholder('-')
                                ->icon('heroicon-o-document-text'),
                        ])->columns(2),
                ])
                ->columns(1),
        ];
    }

    public static function invoicingTabInfolist(): Tab
    {
        return Tab::make(__('accounts::filament/resources/partner.infolist.tabs.invoicing.title'))
            ->icon('heroicon-o-receipt-percent')
            ->schema([
                Fieldset::make(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.customer-invoices.title'))
                    ->schema([
                        TextEntry::make('invoice_sending_method')
                            ->label(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.customer-invoices.entries.invoice-sending-method'))
                            ->placeholder('-')
                            ->icon('heroicon-o-paper-airplane'),
                        TextEntry::make('invoice_edi_format_store')
                            ->label(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.customer-invoices.entries.invoice-edi-format-store'))
                            ->placeholder('-')
                            ->icon('heroicon-o-document'),
                        Group::make()
                            ->schema([
                                TextEntry::make('peppol_eas')
                                    ->label(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.customer-invoices.entries.peppol-eas'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-identification'),
                                TextEntry::make('peppol_endpoint')
                                    ->label(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.customer-invoices.entries.endpoint'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-globe-alt'),
                            ])->columns(2),
                    ]),

                Fieldset::make(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.accounting-entries.title'))
                    ->schema([
                        TextEntry::make('propertyAccountReceivable.name')
                            ->label(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.accounting-entries.entries.account-receivable'))
                            ->placeholder('-'),
                        TextEntry::make('propertyAccountPayable.name')
                            ->label(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.accounting-entries.entries.account-payable'))
                            ->placeholder('-'),
                    ]),

                Fieldset::make(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.automation.title'))
                    ->schema([
                        TextEntry::make('autopost_bills')
                            ->label(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.automation.entries.auto-post-bills'))
                            ->placeholder('-')
                            ->icon('heroicon-o-bolt'),
                        IconEntry::make('ignore_abnormal_invoice_amount')
                            ->boolean()
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.automation.entries.ignore-abnormal-invoice-amount')),
                        IconEntry::make('ignore_abnormal_invoice_date')
                            ->boolean()
                            ->placeholder('-')
                            ->label(__('accounts::filament/resources/partner.infolist.tabs.invoicing.fieldsets.automation.entries.ignore-abnormal-invoice-date')),
                    ]),
            ]);
    }

    public static function internalNotesTabInfolist(): Tab
    {
        return Tab::make(__('accounts::filament/resources/partner.infolist.tabs.internal-notes.title'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                TextEntry::make('comment')
                    ->hiddenLabel()
                    ->html()
                    ->placeholder('-')
                    ->icon('heroicon-o-chat-bubble-left-right'),
            ]);
    }
}
