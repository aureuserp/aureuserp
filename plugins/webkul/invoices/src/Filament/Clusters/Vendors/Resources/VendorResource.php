<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Tables\Table;
use Webkul\Invoice\Enums;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\RelationManagers;
use Webkul\Invoice\Models\Partner;
use Webkul\Partner\Filament\Resources\PartnerResource as BaseVendorResource;

class VendorResource extends BaseVendorResource
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $model = Partner::class;

    protected static ?string $slug = '';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 4;

    protected static ?string $cluster = Vendors::class;

    protected static SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

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

    public static function form(Form $form): Form
    {
        $form = parent::form($form);

        $secondChildComponents = $form->getComponents()[1];

        $saleAndPurchaseComponent = $secondChildComponents->getChildComponents()[0];

        $firstTabFirstChildComponent = $saleAndPurchaseComponent->getChildComponents()[0];

        $firstTabFirstChildComponent->childComponents([
            Forms\Components\Group::make()
                ->schema([
                    Forms\Components\Hidden::make('sub_type')
                        ->default('supplier'),
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.sales-person')),
                    Forms\Components\Select::make('property_payment_term_id')
                        ->relationship('propertyPaymentTerm', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.payment-terms')),
                    Forms\Components\Select::make('property_inbound_payment_method_line_id')
                        ->relationship('propertyInboundPaymentMethodLine', 'name')
                        ->preload()
                        ->searchable()
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.payment-method')),
                ])
                ->columns(2),
        ]);

        $purchaseComponents = Forms\Components\Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.purchase'))
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('property_supplier_payment_term_id')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.payment-terms'))
                            ->relationship('propertySupplierPaymentTerm', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('property_outbound_payment_method_line_id')
                            ->relationship('propertyOutboundPaymentMethodLine', 'name')
                            ->preload()
                            ->searchable()
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.payment-method')),
                    ])->columns(2),
            ])
            ->columns(1);

        $fiscalInformation = Forms\Components\Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.fiscal-information'))
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('property_account_position_id')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.fields.fiscal-position'))
                            ->relationship('propertyAccountPosition', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2),
            ])
            ->columns(1);

        $saleAndPurchaseComponent->childComponents([
            $saleAndPurchaseComponent->getChildComponents()[0],
            $purchaseComponents,
            $fiscalInformation,
            $saleAndPurchaseComponent->getChildComponents()[1],
        ]);

        $invoicingComponent = Forms\Components\Tabs\Tab::make(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.title'))
            ->icon('heroicon-o-receipt-percent')
            ->schema([
                Forms\Components\Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.customer-invoices'))
                    ->schema([
                        Forms\Components\Select::make('invoice_sending_method')
                            ->label('Invoice Sending Method')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.invoice-sending-method'))
                            ->options(Enums\InvoiceSendingMethod::class),
                        Forms\Components\Select::make('invoice_edi_format_store')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.invoice-edi-format-store'))
                            ->live()
                            ->options(Enums\InvoiceFormat::class),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('peppol_eas')
                                    ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.peppol-eas'))
                                    ->live()
                                    ->visible(fn (Get $get) => $get('invoice_edi_format_store') !== Enums\InvoiceFormat::FACTURX_X_CII->value && ! empty($get('invoice_edi_format_store')))
                                    ->options(Enums\PartyIdentificationScheme::class),
                                Forms\Components\TextInput::make('peppol_endpoint')
                                    ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.endpoint'))
                                    ->live()
                                    ->visible(fn (Get $get) => $get('invoice_edi_format_store') !== Enums\InvoiceFormat::FACTURX_X_CII->value && ! empty($get('invoice_edi_format_store'))),
                            ])->columns(2),
                    ]),

                Forms\Components\Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.automation'))
                    ->schema([
                        Forms\Components\Select::make('autopost_bills')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.auto-post-bills'))
                            ->options(Enums\AutoPostBills::class),
                        Forms\Components\Toggle::make('ignore_abnormal_invoice_amount')
                            ->inline(false)
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.ignore-abnormal-invoice-amount')),
                        Forms\Components\Toggle::make('ignore_abnormal_invoice_date')
                            ->inline(false)
                            ->label('Ignore abnormal invoice date')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.invoicing.fields.ignore-abnormal-invoice-date')),
                    ]),
            ]);

        $internalNotes = Forms\Components\Tabs\Tab::make(__('invoices::filament/clusters/vendors/resources/vendor.form.tabs.internal-notes.title'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                Forms\Components\RichEditor::make('comment')
                    ->hiddenLabel(),
            ]);

        $secondChildComponents->childComponents([
            $saleAndPurchaseComponent,
            $invoicingComponent,
            $internalNotes,
        ]);

        return $form;
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
                RelationManagers\BankAccountsRelationManager::class,
            ])
                ->icon('heroicon-o-banknotes'),
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        $infolist = parent::infolist($infolist);

        $secondChildComponents = $infolist->getComponents()[1];

        $saleAndPurchaseComponent = $secondChildComponents->getChildComponents()[0];

        $firstTabFirstChildComponent = $saleAndPurchaseComponent->getChildComponents()[0];

        $firstTabFirstChildComponent->childComponents([
            Infolists\Components\Group::make()
                ->schema([
                    Infolists\Components\TextEntry::make('user.name')
                        ->placeholder('-')
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.sales-person'))
                        ->icon('heroicon-o-user'),
                    Infolists\Components\TextEntry::make('propertyPaymentTerm.name')
                        ->placeholder('-')
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.payment-terms'))
                        ->icon('heroicon-o-calendar'),
                    Infolists\Components\TextEntry::make('propertyInboundPaymentMethodLine.name')
                        ->placeholder('-')
                        ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.payment-method'))
                        ->icon('heroicon-o-credit-card'),
                ])
                ->columns(2),
        ]);

        $purchaseComponents = Infolists\Components\Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.purchase'))
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('propertySupplierPaymentTerm.name')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.payment-terms'))
                            ->placeholder('-')
                            ->icon('heroicon-o-calendar'),
                        Infolists\Components\TextEntry::make('propertyOutboundPaymentMethodLine.name')
                            ->placeholder('-')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.payment-method'))
                            ->icon('heroicon-o-banknotes'),
                    ])->columns(2),
            ])
            ->columns(1);

        $fiscalInformation = Infolists\Components\Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.fiscal-information'))
            ->schema([
                Infolists\Components\Group::make()
                    ->schema([
                        Infolists\Components\TextEntry::make('propertyAccountPosition.name')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.entries.fiscal-position'))
                            ->placeholder('-')
                            ->icon('heroicon-o-document-text'),
                    ])->columns(2),
            ])
            ->columns(1);

        $saleAndPurchaseComponent->childComponents([
            $saleAndPurchaseComponent->getChildComponents()[0],
            $purchaseComponents,
            $fiscalInformation,
            $saleAndPurchaseComponent->getChildComponents()[1],
        ]);

        $invoicingComponent = Infolists\Components\Tabs\Tab::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.title'))
            ->icon('heroicon-o-receipt-percent')
            ->schema([
                Infolists\Components\Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.customer-invoices'))
                    ->schema([
                        Infolists\Components\TextEntry::make('invoice_sending_method')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.invoice-sending-method'))
                            ->placeholder('-')
                            ->icon('heroicon-o-paper-airplane'),
                        Infolists\Components\TextEntry::make('invoice_edi_format_store')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.invoice-edi-format-store'))
                            ->placeholder('-')
                            ->icon('heroicon-o-document'),
                        Infolists\Components\Group::make()
                            ->schema([
                                Infolists\Components\TextEntry::make('peppol_eas')
                                    ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.peppol-eas'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-identification'),
                                Infolists\Components\TextEntry::make('peppol_endpoint')
                                    ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.endpoint'))
                                    ->placeholder('-')
                                    ->icon('heroicon-o-globe-alt'),
                            ])->columns(2),
                    ]),

                Infolists\Components\Fieldset::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.automation'))
                    ->schema([
                        Infolists\Components\TextEntry::make('autopost_bills')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.auto-post-bills'))
                            ->placeholder('-')
                            ->icon('heroicon-o-bolt'),
                        Infolists\Components\IconEntry::make('ignore_abnormal_invoice_amount')
                            ->boolean()
                            ->placeholder('-')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.ignore-abnormal-invoice-amount')),
                        Infolists\Components\IconEntry::make('ignore_abnormal_invoice_date')
                            ->boolean()
                            ->placeholder('-')
                            ->label(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.invoicing.entries.ignore-abnormal-invoice-date')),
                    ]),
            ]);

        $internalNotes = Infolists\Components\Tabs\Tab::make(__('invoices::filament/clusters/vendors/resources/vendor.infolist.tabs.internal-notes.title'))
            ->icon('heroicon-o-chat-bubble-left-right')
            ->schema([
                Infolists\Components\TextEntry::make('comment')
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

        return $infolist;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewVendor::class,
            Pages\EditVendor::class,
            Pages\ManageContacts::class,
            Pages\ManageAddresses::class,
            Pages\ManageBankAccounts::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'        => Pages\ListVendors::route('/'),
            'create'       => Pages\CreateVendor::route('/create'),
            'edit'         => Pages\EditVendor::route('/{record}/edit'),
            'view'         => Pages\ViewVendor::route('/{record}'),
            'contacts'     => Pages\ManageContacts::route('/{record}/contacts'),
            'addresses'    => Pages\ManageAddresses::route('/{record}/addresses'),
            'bank-account' => Pages\ManageBankAccounts::route('/{record}/bank-accounts'),
        ];
    }
}
