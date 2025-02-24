<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Infolists\Infolist;
use Filament\Tables\Table;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Webkul\Invoice\Filament\Clusters\Vendors;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\Pages;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\VendorResource\RelationManagers;
use Webkul\Account\Models\Partner;
use Webkul\Invoice\Enums;
use Webkul\Contact\Filament\Resources\PartnerResource as BaseVendorResource;

class VendorResource extends BaseVendorResource
{
    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $model = Partner::class;

    protected static ?string $slug = '';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 5;

    protected static ?string $cluster = Vendors::class;

    public static function getNavigationLabel(): string
    {
        return __('Vendors');
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
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->preload()
                        ->searchable()
                        ->label('Sales Person'),
                    Forms\Components\Select::make('property_payment_term_id')
                        ->relationship('propertyPaymentTerm', 'name')
                        ->preload()
                        ->searchable()
                        ->label('Payment Terms'),
                    Forms\Components\Select::make('property_inbound_payment_method_line_id')
                        ->relationship('propertyInboundPaymentMethodLine', 'name')
                        ->preload()
                        ->searchable()
                        ->label('Payment Method'),
                ])
                ->columns(2),
        ]);

        $purchaseComponents = Forms\Components\Fieldset::make('Purchase')
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('property_supplier_payment_term_id')
                            ->label(__('Payment Terms'))
                            ->relationship('propertySupplierPaymentTerm', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('property_outbound_payment_method_line_id')
                            ->relationship('propertyOutboundPaymentMethodLine', 'name')
                            ->preload()
                            ->searchable()
                            ->label('Payment Method'),
                    ])->columns(2)
            ])
            ->columns(1);

        $fiscalInformation = Forms\Components\Fieldset::make('Fiscal Information')
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Select::make('property_account_position_id')
                            ->label(__('Fiscal Information'))
                            ->relationship('propertyAccountPosition', 'name')
                            ->searchable()
                            ->preload(),
                    ])->columns(2)
            ])
            ->columns(1);

        $saleAndPurchaseComponent->childComponents([
            $saleAndPurchaseComponent->getChildComponents()[0],
            $purchaseComponents,
            $fiscalInformation,
            $saleAndPurchaseComponent->getChildComponents()[1],
        ]);

        $invoicingComponent = Forms\Components\Tabs\Tab::make('Invoicing')
            ->schema([
                Forms\Components\Fieldset::make('Customer Invoices')
                    ->schema([
                        Forms\Components\Select::make('invoice_sending_method')
                            ->label('Invoice Sending Method')
                            ->options(Enums\InvoiceSendingMethod::class),
                        Forms\Components\Select::make('invoice_edi_format_store')
                            ->label('eInvoice Format')
                            ->live()
                            ->options(Enums\InvoiceFormat::class),
                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Select::make('peppol_eas')
                                    ->label('Peppol Address')
                                    ->live()
                                    ->visible(fn(Get $get) => $get('invoice_edi_format_store') !== Enums\InvoiceFormat::FACTURX_X_CII->value && !empty($get('invoice_edi_format_store')))
                                    ->options(Enums\PartyIdentificationScheme::class),
                                Forms\Components\TextInput::make('peppol_eas')
                                    ->label('Endpoint')
                                    ->placeholder('Endpoint')
                                    ->live()
                                    ->visible(fn(Get $get) => $get('invoice_edi_format_store') !== Enums\InvoiceFormat::FACTURX_X_CII->value && !empty($get('invoice_edi_format_store')))
                            ])->columns(2)
                    ]),

                Forms\Components\Fieldset::make('Automation')
                    ->schema([
                        Forms\Components\Select::make('autopost_bills')
                            ->label('Auto Post Bills')
                            ->options(Enums\AutoPostBills::class),
                        Forms\Components\Toggle::make('ignore_abnormal_invoice_amount')
                            ->inline(false)
                            ->label('Ignore abnormal invoice amount'),
                        Forms\Components\Toggle::make('ignore_abnormal_invoice_date')
                            ->inline(false)
                            ->label('Ignore abnormal invoice date'),
                    ]),
            ]);

        $internalNotes = Forms\Components\Tabs\Tab::make('Internal Notes')
            ->schema([
                Forms\Components\RichEditor::make('comment')
                    ->hiddenLabel()
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
