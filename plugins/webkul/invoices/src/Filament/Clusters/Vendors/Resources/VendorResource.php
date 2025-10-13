<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Forms\Components\Hidden;
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
use Webkul\Invoice\Enums\AutoPostBills;
use Webkul\Invoice\Enums\InvoiceFormat;
use Webkul\Invoice\Enums\InvoiceSendingMethod;
use Webkul\Invoice\Enums\PartyIdentificationScheme;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\CreateVendor;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\EditVendor;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ListVendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageAddresses;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageBankAccounts;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ManageContacts;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages\ViewVendor;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\RelationManagers\BankAccountsRelationManager;
use Webkul\Invoice\Models\Partner;
use Webkul\Partner\Filament\Resources\PartnerResource as BaseVendorResource;

class VendorResource extends BaseVendorResource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static ?string $model = Partner::class;

    protected static ?string $slug = '';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Vendors::class;

    public static function getModelLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/vendor.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices::filament/clusters/vendors/resources/vendor.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return null;
    }

    public static function form(Schema $schema): Schema
    {
        $schema = parent::form($schema);

        $secondChildComponents = $schema->getComponents()[1];

        $saleAndPurchaseComponent = $secondChildComponents->getDefaultChildComponents()[0];

        $firstTabFirstChildComponent = $saleAndPurchaseComponent->getDefaultChildComponents()[0];

        $firstTabFirstChildComponent->childComponents([
            Group::make()
                ->schema([
                    Hidden::make('sub_type')
                        ->default('supplier'),
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.sales-person')),
                    Select::make('property_payment_term_id')
                        ->relationship('propertyPaymentTerm', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.payment-terms')),
                    Select::make('property_inbound_payment_method_line_id')
                        ->relationship('propertyInboundPaymentMethodLine', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.payment-method')),
                ])
                ->columns(2),
        ]);

        $purchaseComponents = Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.purchase'))
            ->schema([
                Group::make()
                    ->schema([
                        Select::make('property_supplier_payment_term_id')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.payment-terms'))
                            ->relationship('propertySupplierPaymentTerm', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('property_outbound_payment_method_line_id')
                            ->relationship('propertyOutboundPaymentMethodLine', 'name')
                            ->preload()
                            ->searchable()
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.payment-method')),
                    ])->columns(2),
            ])
            ->columns(1);

        $fiscalInformation = Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.fiscal-information'))
            ->schema([
                Group::make()
                    ->schema([
                        Select::make('property_account_position_id')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.fiscal-position'))
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

        $invoicingComponent = Tab::make(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.title'))
            ->icon('heroicon-o-receipt-percent')
            ->schema([
                Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.customer-invoices'))
                    ->schema([
                        Select::make('invoice_sending_method')
                            ->label('Invoice Sending Method')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.invoice-sending-method'))
                            ->options(InvoiceSendingMethod::class),
                        Select::make('invoice_edi_format_store')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.invoice-edi-format-store'))
                            ->live()
                            ->options(InvoiceFormat::class),
                        Group::make()
                            ->schema([
                                Select::make('peppol_eas')
                                    ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.peppol-eas'))
                                    ->live()
                                    ->visible(fn (Get $get) => $get('invoice_edi_format_store') !== InvoiceFormat::FACTURX_X_CII->value && ! empty($get('invoice_edi_format_store')))
                                    ->options(PartyIdentificationScheme::class),
                                TextInput::make('peppol_endpoint')
                                    ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.endpoint'))
                                    ->live()
                                    ->visible(fn (Get $get) => $get('invoice_edi_format_store') !== InvoiceFormat::FACTURX_X_CII->value && ! empty($get('invoice_edi_format_store'))),
                            ])->columns(2),
                    ]),

                Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.automation'))
                    ->schema([
                        Select::make('autopost_bills')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.auto-post-bills'))
                            ->options(AutoPostBills::class),
                        Toggle::make('ignore_abnormal_invoice_amount')
                            ->inline(false)
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.ignore-abnormal-invoice-amount')),
                        Toggle::make('ignore_abnormal_invoice_date')
                            ->inline(false)
                            ->label('Ignore abnormal invoice date')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.ignore-abnormal-invoice-date')),
                    ]),
            ]);

        $internalNotes = Tab::make(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.internal-notes.title'))
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

        $table->modifyQueryUsing(fn ($query) => $query->where('sub_type', 'supplier'));

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
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.sales-person'))
                        ->icon('heroicon-o-user'),
                    TextEntry::make('propertyPaymentTerm.name')
                        ->placeholder('-')
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.payment-terms'))
                        ->icon('heroicon-o-calendar'),
                    TextEntry::make('propertyInboundPaymentMethodLine.name')
                        ->placeholder('-')
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.payment-method'))
                        ->icon('heroicon-o-credit-card'),
                ])
                ->columns(2),
        ]);

        $purchaseComponents = Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.purchase'))
            ->schema([
                Group::make()
                    ->schema([
                        TextEntry::make('propertySupplierPaymentTerm.name')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.payment-terms'))
                            ->placeholder('-')
                            ->icon('heroicon-o-calendar'),
                        TextEntry::make('propertyOutboundPaymentMethodLine.name')
                            ->placeholder('-')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.payment-method'))
                            ->icon('heroicon-o-banknotes'),
                    ])->columns(2),
            ])
            ->columns(1);

        $fiscalInformation = Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.fiscal-information'))
            ->schema([
                Group::make()
                    ->schema([
                        TextEntry::make('propertyAccountPosition.name')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.fiscal-position'))
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

        $invoicingComponent = Tab::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.title'))
            ->icon('heroicon-o-receipt-percent')
            ->schema([
                Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.customer-invoices'))
                    ->schema([
                        TextEntry::make('invoice_sending_method')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.invoice-sending-method'))
                            ->placeholder('-')
                            ->icon('heroicon-o-paper-airplane'),
                        TextEntry::make('invoice_edi_format_store')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.invoice-edi-format-store'))
                            ->placeholder('-')
                            ->icon('heroicon-o-document'),
                        Group::make()
                            ->schema([
                                TextEntry::make('peppol_eas')
                                    ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.peppol-eas'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-identification'),
                                TextEntry::make('peppol_endpoint')
                                    ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.endpoint'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-globe-alt'),
                            ])->columns(2),
                    ]),

                Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.automation'))
                    ->schema([
                        TextEntry::make('autopost_bills')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.auto-post-bills'))
                            ->placeholder('-')
                            ->icon('heroicon-o-bolt'),
                        IconEntry::make('ignore_abnormal_invoice_amount')
                            ->boolean()
                            ->placeholder('-')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.ignore-abnormal-invoice-amount')),
                        IconEntry::make('ignore_abnormal_invoice_date')
                            ->boolean()
                            ->placeholder('-')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.ignore-abnormal-invoice-date')),
                    ]),
            ]);

        $internalNotes = Tab::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.internal-notes.title'))
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
            ViewVendor::class,
            EditVendor::class,
            ManageContacts::class,
            ManageAddresses::class,
            ManageBankAccounts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'        => ListVendors::route('/'),
            'create'       => CreateVendor::route('/create'),
            'edit'         => EditVendor::route('/{record}/edit'),
            'view'         => ViewVendor::route('/{record}'),
            'contacts'     => ManageContacts::route('/{record}/contacts'),
            'addresses'    => ManageAddresses::route('/{record}/addresses'),
            'bank-account' => ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
