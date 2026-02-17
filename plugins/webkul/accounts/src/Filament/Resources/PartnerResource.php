<?php

namespace Webkul\Account\Filament\Resources;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\AutoPostBills;
use Webkul\Account\Enums\InvoiceFormat;
use Webkul\Account\Enums\InvoiceSendingMethod;
use Webkul\Account\Enums\PartyIdentificationScheme;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\CreatePartner;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\EditPartner;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ListPartners;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageAddresses;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageBankAccounts;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ManageContacts;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ViewPartner;
use Webkul\Account\Filament\Resources\PartnerResource\RelationManagers\BankAccountsRelationManager;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Partner;
use Webkul\Partner\Filament\Resources\PartnerResource as BasePartnerResource;

class PartnerResource extends BasePartnerResource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $model = Partner::class;

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Schema $schema): Schema
    {
        $schema = parent::form($schema);

        $secondChildComponents = $schema->getComponents()[1];

        $saleAndPurchaseComponent = $secondChildComponents->getDefaultChildComponents()[0];

        $firstTabFirstChildComponent = $saleAndPurchaseComponent->getDefaultChildComponents()[0];

        $firstTabFirstChildComponent->childComponents([
            Group::make()
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.sales.fields.sales-person')),
                    Select::make('property_payment_term_id')
                        ->relationship('propertyPaymentTerm', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.sales.fields.payment-terms')),
                    Select::make('property_inbound_payment_method_line_id')
                        ->relationship('propertyInboundPaymentMethodLine', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.sales.fields.payment-method')),
                ])
                ->columns(2),
        ]);

        $purchaseComponents = Fieldset::make(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.purchase.title'))
            ->schema([
                Group::make()
                    ->schema([
                        Select::make('property_supplier_payment_term_id')
                            ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.purchase.fields.payment-terms'))
                            ->relationship('propertySupplierPaymentTerm', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('property_outbound_payment_method_line_id')
                            ->relationship('propertyOutboundPaymentMethodLine', 'name')
                            ->preload()
                            ->searchable()
                            ->label(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.purchase.fields.payment-method')),
                    ])->columns(2),
            ])
            ->columns(1);

        $fiscalInformation = Fieldset::make(__('accounts::filament/resources/partner.form.tabs.sales-purchases.fieldsets.fiscal-information.title'))
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
            ->columns(1);

        $saleAndPurchaseComponent->childComponents([
            $saleAndPurchaseComponent->getDefaultChildComponents()[0],
            $purchaseComponents,
            $fiscalInformation,
            $saleAndPurchaseComponent->getDefaultChildComponents()[1],
        ]);

        $invoicingComponent = Tab::make(__('accounts::filament/resources/partner.form.tabs.invoicing.title'))
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
                            ->default(Account::where('account_type', AccountType::ASSET_RECEIVABLE)->where('deprecated', false)->first()->id),
                        Select::make('property_account_payable_id')
                            ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.accounting-entries.fields.account-payable'))
                            ->relationship('propertyAccountPayable', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(Account::where('account_type', AccountType::LIABILITY_PAYABLE)->where('deprecated', false)->first()->id),
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
                            ->label('Ignore abnormal invoice date')
                            ->label(__('accounts::filament/resources/partner.form.tabs.invoicing.fieldsets.automation.fields.ignore-abnormal-invoice-date')),
                    ]),
            ]);

        $internalNotes = Tab::make(__('accounts::filament/resources/partner.form.tabs.internal-notes.title'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                RichEditor::make('comment')
                    ->hiddenLabel(),
            ]);

        $secondChildComponents->childComponents([
            $saleAndPurchaseComponent,
            $invoicingComponent,
            $internalNotes,
        ]);

        return $schema;
    }

    public static function table(Table $table): Table
    {
        $table = parent::table($table);

        $table->contentGrid([
            'sm'  => 1,
            'md'  => 2,
            'xl'  => 3,
            '2xl' => 3,
        ]);

        return $table;
    }

    public static function getRelations(): array
    {
        $table = parent::getRelations();

        return [
            ...$table,
            RelationGroup::make('Bank Accounts', [
                BankAccountsRelationManager::class,
            ])
                ->icon('heroicon-o-banknotes'),
        ];
    }

    public static function infolist(Schema $schema): Schema
    {
        $schema = parent::infolist($schema);

        $secondChildComponents = $schema->getComponents()[1];

        $saleAndPurchaseComponent = $secondChildComponents->getDefaultChildComponents()[0];

        $firstTabFirstChildComponent = $saleAndPurchaseComponent->getDefaultChildComponents()[0];

        $firstTabFirstChildComponent->childComponents([
            Group::make()
                ->schema([
                    TextEntry::make('user.name')
                        ->placeholder('-')
                        ->label(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.sales.entries.sales-person'))
                        ->icon('heroicon-o-user'),
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
        ]);

        $purchaseComponents = Fieldset::make(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.purchase.title'))
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
            ->columns(1);

        $fiscalInformation = Fieldset::make(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.fiscal-information.title'))
            ->schema([
                Group::make()
                    ->schema([
                        TextEntry::make('propertyAccountPosition.name')
                            ->label(__('accounts::filament/resources/partner.infolist.tabs.sales-purchases.fieldsets.fiscal-information.entries.fiscal-position'))
                            ->placeholder('-')
                            ->icon('heroicon-o-document-text'),
                    ])->columns(2),
            ])
            ->columns(1);

        $saleAndPurchaseComponent->childComponents([
            $saleAndPurchaseComponent->getDefaultChildComponents()[0],
            $purchaseComponents,
            $fiscalInformation,
            $saleAndPurchaseComponent->getDefaultChildComponents()[1],
        ]);

        $invoicingComponent = Tab::make(__('accounts::filament/resources/partner.infolist.tabs.invoicing.title'))
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
                            ->placeholder('-')
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

        $internalNotes = Tab::make(__('accounts::filament/resources/partner.infolist.tabs.internal-notes.title'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                TextEntry::make('comment')
                    ->hiddenLabel()
                    ->html()
                    ->placeholder('-')
                    ->icon('heroicon-o-chat-bubble-left-right'),
            ]);

        $secondChildComponents->childComponents([
            $saleAndPurchaseComponent,
            $invoicingComponent,
            $internalNotes,
        ]);

        return $schema;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPartner::class,
            EditPartner::class,
            ManageContacts::class,
            ManageAddresses::class,
            ManageBankAccounts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'         => ListPartners::route('/'),
            'create'        => CreatePartner::route('/create'),
            'edit'          => EditPartner::route('/{record}/edit'),
            'view'          => ViewPartner::route('/{record}'),
            'contacts'      => ManageContacts::route('/{record}/contacts'),
            'addresses'     => ManageAddresses::route('/{record}/addresses'),
            'bank-accounts' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
