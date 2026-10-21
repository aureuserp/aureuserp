<?php

namespace Webkul\PointOfSale\Filament\Admin\Clusters\Configurations\Resources\ConfigResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Models\Warehouse;
use Webkul\PointOfSale\Enums\PickingPolicy;
use Webkul\PointOfSale\Enums\SessionState;
use Webkul\PointOfSale\Enums\TaxDisplay;
use Webkul\PointOfSale\Models\Config;
use Webkul\Product\Enums\ProductType;
use Webkul\Support\Models\Company;

class ConfigForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(static::label('sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(static::label('sections.general.fields.name'))
                            ->placeholder(static::label('sections.general.fields.name-placeholder'))
                            ->required()
                            ->maxLength(255)
                            ->autofocus(),

                        TextInput::make('code')
                            ->label(static::label('sections.general.fields.code'))
                            ->helperText(static::label('sections.general.fields.code-helper-text'))
                            ->required()
                            ->maxLength(16),

                    ])
                    ->columns(2),

                Section::make(static::label('sections.configurations.title'))
                    ->schema([
                        Tabs::make()
                            ->tabs([
                                static::restaurantTab(),
                                static::paymentTab(),
                                static::interfaceTab(),
                                static::productsTab(),
                                static::accountingTab(),
                                static::pricingTab(),
                                static::receiptsTab(),
                                static::preparationTab(),
                                static::inventoryTab(),
                            ])
                            ->vertical()
                            ->columnSpanFull(),
                    ])
                    ->columns(1)
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
            ])
            ->columns(1);
    }

    protected static function restaurantTab(): Tab
    {
        return Tab::make(static::label('sections.configurations.tabs.restaurant.title'))
            ->icon('heroicon-o-building-storefront')
            ->schema([
                Toggle::make('is_restaurant')
                    ->label(static::label('sections.configurations.tabs.restaurant.fields.is-restaurant'))
                    ->helperText(static::label('sections.configurations.tabs.restaurant.fields.is-restaurant-helper-text'))
                    ->disabled(fn (?Config $record): bool => static::lockedByOpenSession($record))
                    ->hint(fn (?Config $record): ?string => static::lockedByOpenSessionHint($record))
                    ->live()
                    ->columnSpanFull(),

                Toggle::make('enable_split_bill')
                    ->label(static::label('sections.configurations.tabs.restaurant.fields.enable-split-bill'))
                    ->helperText(static::label('sections.configurations.tabs.restaurant.fields.enable-split-bill-helper-text'))
                    ->visible(fn (Get $get): bool => (bool) $get('is_restaurant')),

                Toggle::make('enable_print_bill')
                    ->label(static::label('sections.configurations.tabs.restaurant.fields.enable-print-bill'))
                    ->helperText(static::label('sections.configurations.tabs.restaurant.fields.enable-print-bill-helper-text'))
                    ->visible(fn (Get $get): bool => (bool) $get('is_restaurant')),

                Toggle::make('enable_takeaway')
                    ->label(static::label('sections.configurations.tabs.restaurant.fields.enable-takeaway'))
                    ->helperText(static::label('sections.configurations.tabs.restaurant.fields.enable-takeaway-helper-text'))
                    ->live()
                    ->visible(fn (Get $get): bool => (bool) $get('is_restaurant')),

                Select::make('takeaway_fiscal_position_id')
                    ->label(static::label('sections.configurations.tabs.restaurant.fields.takeaway-fiscal-position'))
                    ->relationship(
                        'takeawayFiscalPosition',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->visible(fn (Get $get): bool => static::usesTakeaway($get)),

                Select::make('floors')
                    ->label(static::label('sections.configurations.tabs.restaurant.fields.floors'))
                    ->helperText(static::label('sections.configurations.tabs.restaurant.fields.floors-helper-text'))
                    ->relationship('floors', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->visible(fn (Get $get): bool => (bool) $get('is_restaurant'))
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    protected static function paymentTab(): Tab
    {
        return Tab::make(static::label('sections.configurations.tabs.payment.title'))
            ->icon('heroicon-o-banknotes')
            ->schema([
                Select::make('paymentMethods')
                    ->label(static::label('sections.configurations.tabs.payment.fields.payment-methods'))
                    ->relationship(
                        'paymentMethods',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record),
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->disabled(fn (?Config $record): bool => static::lockedByOpenSession($record))
                    ->hint(fn (?Config $record): ?string => static::lockedByOpenSessionHint($record))
                    ->columnSpanFull(),

                Toggle::make('enable_maximum_difference')
                    ->label(static::label('sections.configurations.tabs.payment.fields.enable-maximum-difference'))
                    ->helperText(static::label('sections.configurations.tabs.payment.fields.enable-maximum-difference-helper-text'))
                    ->live(),

                TextInput::make('amount_authorized_diff')
                    ->label(static::label('sections.configurations.tabs.payment.fields.amount-authorized-diff'))
                    ->numeric()
                    ->minValue(0)
                    ->visible(fn (Get $get): bool => (bool) $get('enable_maximum_difference')),

                Toggle::make('enable_cash_rounding')
                    ->label(static::label('sections.configurations.tabs.payment.fields.enable-cash-rounding'))
                    ->helperText(static::label('sections.configurations.tabs.payment.fields.enable-cash-rounding-helper-text'))
                    ->live(),

                Select::make('cash_rounding_id')
                    ->label(static::label('sections.configurations.tabs.payment.fields.cash-rounding'))
                    ->relationship('cashRounding', 'name')
                    ->searchable()
                    ->native(false)
                    ->preload()
                    ->visible(fn (Get $get): bool => (bool) $get('enable_cash_rounding')),

                Toggle::make('enable_only_round_cash_method')
                    ->label(static::label('sections.configurations.tabs.payment.fields.enable-only-round-cash-method'))
                    ->visible(fn (Get $get): bool => (bool) $get('enable_cash_rounding')),

                Toggle::make('enable_tip')
                    ->label(static::label('sections.configurations.tabs.payment.fields.enable-tip'))
                    ->helperText(static::label('sections.configurations.tabs.payment.fields.enable-tip-helper-text'))
                    ->live(),

                Select::make('tip_product_id')
                    ->label(static::label('sections.configurations.tabs.payment.fields.tip-product'))
                    ->relationship(
                        'tipProduct',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record)->where('type', ProductType::SERVICE),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->visible(fn (Get $get): bool => (bool) $get('enable_tip')),
            ])
            ->columns(2);
    }

    protected static function interfaceTab(): Tab
    {
        return Tab::make(static::label('sections.configurations.tabs.interface.title'))
            ->icon('heroicon-o-computer-desktop')
            ->schema([
                Toggle::make('enable_customer_required')
                    ->label(static::label('sections.configurations.tabs.interface.fields.enable-customer-required')),

                Toggle::make('show_product_images')
                    ->label(static::label('sections.configurations.tabs.interface.fields.show-product-images')),

                Toggle::make('show_category_images')
                    ->label(static::label('sections.configurations.tabs.interface.fields.show-category-images')),

                TextInput::make('limited_products_amount')
                    ->label(static::label('sections.configurations.tabs.interface.fields.limited-products-amount'))
                    ->helperText(static::label('sections.configurations.tabs.interface.fields.limited-products-amount-helper-text'))
                    ->numeric()
                    ->minValue(1)
                    ->default(500)
                    ->required(),
            ])
            ->columns(2);
    }

    protected static function productsTab(): Tab
    {
        return Tab::make(static::label('sections.configurations.tabs.products.title'))
            ->icon('heroicon-o-squares-2x2')
            ->schema([
                Toggle::make('limit_categories')
                    ->label(static::label('sections.configurations.tabs.products.fields.limit-categories'))
                    ->helperText(static::label('sections.configurations.tabs.products.fields.limit-categories-helper-text'))
                    ->disabled(fn (?Config $record): bool => static::lockedByOpenSession($record))
                    ->hint(fn (?Config $record): ?string => static::lockedByOpenSessionHint($record))
                    ->live()
                    ->columnSpanFull(),
                Toggle::make('show_margins_and_costs')
                    ->label(static::label('sections.configurations.tabs.products.fields.show-margins-and-costs'))
                    ->helperText(static::label('sections.configurations.tabs.products.fields.show-margins-and-costs-helper-text'))
                    ->columnSpanFull(),

                Select::make('categories')
                    ->label(static::label('sections.configurations.tabs.products.fields.categories'))
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->disabled(fn (?Config $record): bool => static::lockedByOpenSession($record))
                    ->hint(fn (?Config $record): ?string => static::lockedByOpenSessionHint($record))
                    ->visible(fn (Get $get): bool => (bool) $get('limit_categories'))
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    protected static function usesTakeaway(Get $get): bool
    {
        return (bool) $get('is_restaurant') && (bool) $get('enable_takeaway');
    }

    protected static function lockedByOpenSession(?Config $record): bool
    {
        return $record !== null
            && $record->sessions()->whereNot('state', SessionState::CLOSED)->exists();
    }

    protected static function lockedByOpenSessionHint(?Config $record): ?string
    {
        return static::lockedByOpenSession($record)
            ? static::label('sections.configurations.tabs.products.fields.locked-by-session')
            : null;
    }

    protected static function accountingTab(): Tab
    {
        return Tab::make(static::label('sections.configurations.tabs.accounting.title'))
            ->icon('heroicon-o-calculator')
            ->schema([
                Select::make('journal_id')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.journal'))
                    ->helperText(static::label('sections.configurations.tabs.accounting.fields.journal-helper-text'))
                    ->relationship(
                        'journal',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query->whereIn('type', [JournalType::GENERAL, JournalType::SALE]), $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                Select::make('invoice_journal_id')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.invoice-journal'))
                    ->relationship(
                        'invoiceJournal',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query->where('type', JournalType::SALE), $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                Toggle::make('is_closing_entry_by_product')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.is-closing-entry-by-product'))
                    ->helperText(static::label('sections.configurations.tabs.accounting.fields.is-closing-entry-by-product-helper-text')),

                Toggle::make('enable_fiscal_position')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.enable-fiscal-position'))
                    ->helperText(static::label('sections.configurations.tabs.accounting.fields.enable-fiscal-position-helper-text'))
                    ->live()
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => ! static::usesTakeaway($get)),

                Select::make('fiscal_position_id')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.fiscal-position'))
                    ->relationship(
                        'fiscalPosition',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->visible(fn (Get $get): bool => (bool) $get('enable_fiscal_position') || static::usesTakeaway($get)),

                Select::make('fiscalPositions')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.fiscal-positions'))
                    ->relationship(
                        'fiscalPositions',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record),
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->visible(fn (Get $get): bool => (bool) $get('enable_fiscal_position') && ! static::usesTakeaway($get)),

                Select::make('receivable_account_id')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.receivable-account'))
                    ->helperText(static::label('sections.configurations.tabs.accounting.fields.receivable-account-helper-text'))
                    ->relationship(
                        'receivableAccount',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::accounts($query, $record)
                            ->where('account_type', AccountType::ASSET_RECEIVABLE)
                            ->where('reconcile', true),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                Select::make('cash_movement_account_id')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.cash-movement-account'))
                    ->relationship(
                        'cashMovementAccount',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::accounts($query, $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                Select::make('balancing_account_id')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.balancing-account'))
                    ->relationship(
                        'balancingAccount',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::accounts($query, $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                Toggle::make('enable_cogs')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.enable-cogs'))
                    ->helperText(static::label('sections.configurations.tabs.accounting.fields.enable-cogs-helper-text'))
                    ->live()
                    ->columnSpanFull(),

                Select::make('cogs_journal_id')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.cogs-journal'))
                    ->relationship(
                        'cogsJournal',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->visible(fn (Get $get): bool => (bool) $get('enable_cogs')),

                Select::make('stock_output_account_id')
                    ->label(static::label('sections.configurations.tabs.accounting.fields.stock-output-account'))
                    ->relationship(
                        'stockOutputAccount',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::accounts($query, $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->visible(fn (Get $get): bool => (bool) $get('enable_cogs')),
            ])
            ->columns(2);
    }

    protected static function pricingTab(): Tab
    {
        return Tab::make(static::label('sections.configurations.tabs.pricing.title'))
            ->icon('heroicon-o-currency-dollar')
            ->schema([
                Select::make('tax_display')
                    ->label(static::label('sections.configurations.tabs.pricing.fields.tax-display'))
                    ->helperText(static::label('sections.configurations.tabs.pricing.fields.tax-display-helper-text'))
                    ->options(TaxDisplay::class)
                    ->native(false)
                    ->default(TaxDisplay::SUBTOTAL)
                    ->required(),

                Toggle::make('enable_price_control')
                    ->label(static::label('sections.configurations.tabs.pricing.fields.enable-price-control'))
                    ->helperText(static::label('sections.configurations.tabs.pricing.fields.enable-price-control-helper-text')),

                Toggle::make('enable_price_list')
                    ->label(static::label('sections.configurations.tabs.pricing.fields.enable-price-list'))
                    ->helperText(static::label('sections.configurations.tabs.pricing.fields.enable-price-list-helper-text'))
                    ->live()
                    ->columnSpanFull(),

                Select::make('priceLists')
                    ->label(static::label('sections.configurations.tabs.pricing.fields.price-lists'))
                    ->relationship(
                        'priceLists',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::priceListsInCurrency($query, $record),
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->live()
                    ->visible(fn (Get $get): bool => (bool) $get('enable_price_list')),

                Select::make('price_list_id')
                    ->label(static::label('sections.configurations.tabs.pricing.fields.price-list'))
                    ->helperText(static::label('sections.configurations.tabs.pricing.fields.price-list-helper-text'))
                    ->relationship(
                        'priceList',
                        'name',
                        fn (Builder $query, Get $get, ?Config $record): Builder => static::availablePriceLists($query, $get, $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false),

                Toggle::make('enable_line_discount')
                    ->label(static::label('sections.configurations.tabs.pricing.fields.enable-line-discount'))
                    ->helperText(static::label('sections.configurations.tabs.pricing.fields.enable-line-discount-helper-text')),

                Toggle::make('enable_global_discount')
                    ->label(static::label('sections.configurations.tabs.pricing.fields.enable-global-discount'))
                    ->helperText(static::label('sections.configurations.tabs.pricing.fields.enable-global-discount-helper-text'))
                    ->disabled(fn (?Config $record): bool => static::lockedByOpenSession($record))
                    ->hint(fn (?Config $record): ?string => static::lockedByOpenSessionHint($record))
                    ->live(),

                Select::make('discount_product_id')
                    ->label(static::label('sections.configurations.tabs.pricing.fields.discount-product'))
                    ->relationship(
                        'discountProduct',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record)->where('type', ProductType::SERVICE),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(fn (Get $get): bool => (bool) $get('enable_global_discount'))
                    ->visible(fn (Get $get): bool => (bool) $get('enable_global_discount')),

                TextInput::make('global_discount_percentage')
                    ->label(static::label('sections.configurations.tabs.pricing.fields.global-discount-percentage'))
                    ->helperText(static::label('sections.configurations.tabs.pricing.fields.global-discount-percentage-helper-text'))
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(10)
                    ->suffix('%')
                    ->required(fn (Get $get): bool => (bool) $get('enable_global_discount'))
                    ->visible(fn (Get $get): bool => (bool) $get('enable_global_discount')),
            ])
            ->columns(2);
    }

    protected static function receiptsTab(): Tab
    {
        return Tab::make(static::label('sections.configurations.tabs.receipts.title'))
            ->icon('heroicon-o-receipt-percent')
            ->schema([
                Toggle::make('enable_receipt_print')
                    ->label(static::label('sections.configurations.tabs.receipts.fields.enable-receipt-print'))
                    ->helperText(static::label('sections.configurations.tabs.receipts.fields.enable-receipt-print-helper-text'))
                    ->live(),

                Toggle::make('enable_receipt_auto_print')
                    ->label(static::label('sections.configurations.tabs.receipts.fields.enable-receipt-auto-print'))
                    ->helperText(static::label('sections.configurations.tabs.receipts.fields.enable-receipt-auto-print-helper-text'))
                    ->visible(fn (Get $get): bool => (bool) $get('enable_receipt_print')),

                Textarea::make('receipt_header')
                    ->label(static::label('sections.configurations.tabs.receipts.fields.receipt-header'))
                    ->rows(2),

                Textarea::make('receipt_footer')
                    ->label(static::label('sections.configurations.tabs.receipts.fields.receipt-footer'))
                    ->rows(2),

                Select::make('bills')
                    ->label(static::label('sections.configurations.tabs.receipts.fields.bills'))
                    ->helperText(static::label('sections.configurations.tabs.receipts.fields.bills-helper-text'))
                    ->relationship('bills', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    protected static function preparationTab(): Tab
    {
        return Tab::make(static::label('sections.configurations.tabs.preparation.title'))
            ->icon('heroicon-o-printer')
            ->schema([
                Select::make('printers')
                    ->label(static::label('sections.configurations.tabs.preparation.fields.printers'))
                    ->helperText(static::label('sections.configurations.tabs.preparation.fields.printers-helper-text'))
                    ->relationship(
                        'printers',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record),
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->columnSpanFull(),
            ])
            ->columns(1);
    }

    protected static function inventoryTab(): Tab
    {
        return Tab::make(static::label('sections.configurations.tabs.inventory.title'))
            ->icon('heroicon-o-cube')
            ->schema([
                Select::make('warehouse_id')
                    ->label(static::label('sections.configurations.tabs.inventory.fields.warehouse'))
                    ->relationship(
                        'warehouse',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record),
                    )
                    ->default(fn (): ?int => static::scoped(Warehouse::query(), null)->orderBy('id')->value('id'))
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function (Set $set): void {
                        $set('operation_type_id', null);
                        $set('return_operation_type_id', null);
                    })
                    ->required(),

                Select::make('operation_type_id')
                    ->label(static::label('sections.configurations.tabs.inventory.fields.operation-type'))
                    ->helperText(static::label('sections.configurations.tabs.inventory.fields.operation-type-helper-text'))
                    ->relationship(
                        'operationType',
                        'name',
                        fn (Builder $query, Get $get, ?Config $record): Builder => static::scoped($query->where('type', OperationTypeEnum::OUTGOING)->where('warehouse_id', $get('warehouse_id')), $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->disabled(fn (Get $get): bool => blank($get('warehouse_id')))
                    ->required(fn (string $operation): bool => $operation !== 'create'),

                Select::make('return_operation_type_id')
                    ->label(static::label('sections.configurations.tabs.inventory.fields.return-operation-type'))
                    ->relationship(
                        'returnOperationType',
                        'name',
                        fn (Builder $query, Get $get, ?Config $record): Builder => static::scoped($query->where('type', OperationTypeEnum::INCOMING)->where('warehouse_id', $get('warehouse_id')), $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->disabled(fn (Get $get): bool => blank($get('warehouse_id'))),

                Toggle::make('enable_ship_later')
                    ->label(static::label('sections.configurations.tabs.inventory.fields.enable-ship-later'))
                    ->helperText(static::label('sections.configurations.tabs.inventory.fields.enable-ship-later-helper-text'))
                    ->live(),

                Select::make('ship_later_route_id')
                    ->label(static::label('sections.configurations.tabs.inventory.fields.ship-later-route'))
                    ->relationship(
                        'shipLaterRoute',
                        'name',
                        fn (Builder $query, ?Config $record): Builder => static::scoped($query, $record),
                    )
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->visible(fn (Get $get): bool => (bool) $get('enable_ship_later')),

                Select::make('picking_policy')
                    ->label(static::label('sections.configurations.tabs.inventory.fields.picking-policy'))
                    ->options(PickingPolicy::class)
                    ->native(false)
                    ->default(PickingPolicy::DIRECT)
                    ->required()
                    ->visible(fn (Get $get): bool => (bool) $get('enable_ship_later')),
            ])
            ->columns(2);
    }

    protected static function accounts(Builder $query, ?Config $record): Builder
    {
        return static::scoped($query, $record)->where(fn (Builder $accounts) => $accounts
            ->where('deprecated', false)
            ->orWhereNull('deprecated'));
    }

    protected static function priceListsInCurrency(Builder $query, ?Config $record): Builder
    {
        $currencyId = static::currencyId($record);

        return static::scoped($query, $record)
            ->when($currencyId, fn (Builder $lists): Builder => $lists->where('currency_id', $currencyId));
    }

    protected static function currencyId(?Config $record): ?int
    {
        return $record?->currency_id
            ?? $record?->journal?->currency_id
            ?? Company::find(current_company_id())?->currency_id;
    }

    protected static function availablePriceLists(Builder $query, Get $get, ?Config $record): Builder
    {
        if (! $get('enable_price_list')) {
            return static::scoped($query, $record);
        }

        $available = array_filter((array) $get('priceLists'));

        return static::priceListsInCurrency($query, $record)
            ->whereIn('id', $available ?: [0]);
    }

    protected static function scoped(Builder $query, ?Config $record): Builder
    {
        return $query->where(owned_by_company($record?->company_id ?? current_company_id()));
    }

    protected static function label(string $key): string
    {
        return __("point-of-sale::filament/admin/clusters/configurations/resources/config.form.{$key}");
    }
}
