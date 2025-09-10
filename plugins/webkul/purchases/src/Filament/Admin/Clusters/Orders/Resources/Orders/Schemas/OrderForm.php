<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax as TaxFacade;
use Webkul\Account\Filament\Resources\IncoTerms\IncoTermResource;
use Webkul\Account\Models\Partner;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Packaging;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\QtyReceivedMethod;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Orders\OrderResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\Vendors\VendorResource;
use Webkul\Purchase\Livewire\Summary;
use Webkul\Purchase\Models\Product;
use Webkul\Purchase\Settings\OrderSettings;
use Webkul\Purchase\Settings\ProductSettings;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;
use Webkul\Support\Package;

class OrderForm
{
    use HasCustomFields;

    public static function getModel()
    {
        return OrderResource::getModel();
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = OrderState::options();

                        if ($record && $record->state !== OrderState::CANCELED) {
                            unset($options[OrderState::CANCELED->value]);
                        }

                        if ($record && $record->state !== OrderState::DONE) {
                            unset($options[OrderState::DONE->value]);
                        }

                        return $options;
                    })
                    ->default(OrderState::DRAFT)
                    ->disabled(),
                Section::make(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                    )
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => VendorResource::form($schema))
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if ($state) {
                                            $vendor = Partner::find($state);

                                            $set('payment_term_id', $vendor->property_supplier_payment_term_id);

                                            $products = $get('products');
                                            if (is_array($products)) {
                                                foreach ($products as $key => $product) {
                                                    if (isset($product['product_id'])) {
                                                        $productModel = Product::find($product['product_id']);
                                                        if ($productModel) {
                                                            $vendorPrices = $productModel->supplierInformation
                                                                ->where('partner_id', $state)
                                                                ->where('currency_id', $get('currency_id'))
                                                                ->where('min_qty', '<=', $product['product_qty'] ?? 1)
                                                                ->sortByDesc('sort');

                                                            if ($vendorPrices->isNotEmpty()) {
                                                                $vendorPrice = $vendorPrices->first()->price;
                                                            } else {
                                                                $vendorPrice = $productModel->cost ?? $productModel->price;
                                                            }

                                                            $set("products.$key.price_unit", round($vendorPrice, 2));

                                                            self::calculateLineTotals($set, $get, "products.$key.");
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    })
                                    ->live()
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                TextInput::make('partner_reference')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor-reference'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor-reference-tooltip')),
                                Select::make('requisition_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.agreement'))
                                    ->relationship('requisition', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (OrderSettings $setting): bool => $setting->enable_purchase_agreements),
                                Select::make('currency_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.currency'))
                                    ->relationship('currency', 'name')
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->default(Auth::user()->defaultCompany?->currency_id)
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                            ]),

                        Group::make()
                            ->schema([
                                DateTimePicker::make('approved_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.confirmation-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->disabled()
                                    ->visible(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                DateTimePicker::make('ordered_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.order-deadline'))
                                    ->native(false)
                                    ->required()
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->hidden(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                DateTimePicker::make('planned_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.expected-arrival'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->hint('Test')
                                    ->hint(fn ($record): string => $record && $record->mail_reminder_confirmed ? __('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.confirmed-by-vendor') : '')
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                            ]),
                    ])
                    ->columns(2),

                Tabs::make()
                    ->schema([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.title'))
                            ->schema([
                                static::getProductRepeater(),
                                Livewire::make(Summary::class, function (Get $get) {
                                    return [
                                        'currency' => Currency::find($get('currency_id')),
                                        'products' => $get('products'),
                                    ];
                                })
                                    ->live()
                                    ->reactive(),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.title'))
                            ->schema(static::mergeCustomFormFields([
                                Group::make()
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.buyer'))
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::id())
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                        Select::make('company_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->default(Auth::user()->default_company_id)
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                        TextInput::make('reference')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.source-document'))
                                            ->maxLength(255),
                                        Select::make('incoterm_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm'))
                                            ->relationship('incoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema) => IncoTermResource::form($schema))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm-tooltip'))
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                        TextInput::make('reference')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm-location'))
                                            ->maxLength(255)
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                    ]),

                                Group::make()
                                    ->schema([
                                        Select::make('payment_term_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.payment-term'))
                                            ->relationship('paymentTerm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                    ]),
                            ]))
                            ->columns(2),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.terms.title'))
                            ->schema([
                                RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getProductRepeater(): Repeater
    {
        return Repeater::make('products')
            ->relationship('lines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.title'))
            ->addActionLabel(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.add-product-line'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->deletable(fn ($record): bool => ! in_array($record?->state, [OrderState::DONE, OrderState::CANCELED]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [OrderState::DONE, OrderState::CANCELED]))
            ->schema([
                Group::make()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn ($query) => $query->where('type', ProductType::GOODS)->whereNull('is_configurable'),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        static::afterProductUpdated($set, $get);
                                    })
                                    ->required()
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::SENT, OrderState::PURCHASE, OrderState::DONE, OrderState::CANCELED])),
                                DateTimePicker::make('planned_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.expected-arrival'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->required()
                                    ->default(now())
                                    ->default(function (Get $get, Set $set) {
                                        if (empty($get('../../planned_at'))) {
                                            $set('../../planned_at', now());
                                        }

                                        return now();
                                    })
                                    ->afterStateUpdated(function (?string $state, Set $set) {
                                        $set('../../planned_at', $state);
                                    })
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                                TextInput::make('product_qty')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.quantity'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        static::afterProductQtyUpdated($set, $get);
                                    })
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                                TextInput::make('qty_received')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.received'))
                                    ->required()
                                    ->default(0)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED]) || $record?->qty_received_method == QtyReceivedMethod::STOCK_MOVE),
                                TextInput::make('qty_invoiced')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.billed'))
                                    ->default(0)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE]))
                                    ->disabled(),
                                Select::make('uom_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit'))
                                    ->relationship(
                                        'uom',
                                        'name',
                                        fn ($query) => $query->where('category_id', 1)->orderBy('id'),
                                    )
                                    ->required()
                                    ->live()
                                    ->selectablePlaceholder(false)
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        static::afterUOMUpdated($set, $get);
                                    })
                                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::PURCHASE, OrderState::DONE, OrderState::CANCELED])),
                                TextInput::make('product_packaging_qty')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging-qty'))
                                    ->live()
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        static::afterProductPackagingQtyUpdated($set, $get);
                                    })
                                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                                Select::make('product_packaging_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.packaging'))
                                    ->relationship(
                                        'productPackaging',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        static::afterProductPackagingUpdated($set, $get);
                                    })
                                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                                TextInput::make('price_unit')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.unit-price'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::calculateLineTotals($set, $get);
                                    })
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                                Select::make('taxes')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.taxes'))
                                    ->relationship(
                                        'taxes',
                                        'name',
                                        function (Builder $query) {
                                            return $query->where('type_tax_use', TypeTaxUse::PURCHASE->value);
                                        },
                                    )
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                        self::calculateLineTotals($set, $get);
                                    })
                                    ->live()
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                                TextInput::make('discount')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.discount-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        self::calculateLineTotals($set, $get);
                                    })
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                                TextInput::make('price_subtotal')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.repeater.products.fields.amount'))
                                    ->default(0)
                                    ->readOnly()
                                    ->disabled(fn ($record): bool => in_array($record?->order->state, [OrderState::DONE, OrderState::CANCELED])),
                                Hidden::make('product_uom_qty')
                                    ->default(0),
                                Hidden::make('price_tax')
                                    ->default(0),
                                Hidden::make('price_total')
                                    ->default(0),
                            ])->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(function (array $data, $record) {
                $product = Product::find($data['product_id']);

                $qtyReceivedMethod = QtyReceivedMethod::MANUAL;

                if (Package::isPluginInstalled('inventories')) {
                    $qtyReceivedMethod = QtyReceivedMethod::STOCK_MOVE;
                }

                $data = array_merge($data, [
                    'name'                => $product->name,
                    'state'               => $record->state->value,
                    'qty_received_method' => $qtyReceivedMethod,
                    'uom_id'              => $data['uom_id'] ?? $product->uom_id,
                    'currency_id'         => $record->currency_id,
                    'partner_id'          => $record->partner_id,
                    'creator_id'          => Auth::id(),
                    'company_id'          => Auth::user()->default_company_id,
                ]);

                return $data;
            });
    }

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('uom_id', $product->uom_id);

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', $product->productTaxes->pluck('id')->toArray());

        $packaging = static::getBestPackaging($get('product_id'), round($uomQuantity, 2));

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $uomQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        self::calculateLineTotals($set, $get);
    }

    private static function afterUOMUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('uom_id'), $get('product_qty'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $uomQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductPackagingQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        if ($get('product_packaging_id')) {
            $packaging = Packaging::find($get('product_packaging_id'));

            $packagingQty = floatval($get('product_packaging_qty') ?? 0);

            $productUOMQty = $packagingQty * $packaging->qty;

            $set('product_uom_qty', round($productUOMQty, 2));

            $uom = Uom::find($get('uom_id'));

            $productQty = $uom ? $productUOMQty * $uom->factor : $productUOMQty;

            $set('product_qty', round($productQty, 2));
        }

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductPackagingUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        if ($get('product_packaging_id')) {
            $packaging = Packaging::find($get('product_packaging_id'));

            $productUOMQty = $get('product_uom_qty') ?: 1;

            if ($packaging) {
                $packagingQty = $productUOMQty / $packaging->qty;

                $set('product_packaging_qty', $packagingQty);
            }
        } else {
            $set('product_packaging_qty', null);
        }

        self::calculateLineTotals($set, $get);
    }

    private static function calculateUnitQuantity($uomId, $quantity)
    {
        if (! $uomId) {
            return $quantity;
        }

        $uom = Uom::find($uomId);

        return (float) ($quantity ?? 0) / $uom->factor;
    }

    private static function calculateUnitPrice($get)
    {
        $product = Product::find($get('product_id'));

        $vendorPrices = $product->supplierInformation->sortByDesc('sort');

        if ($get('../../partner_id')) {
            $vendorPrices = $vendorPrices->where('partner_id', $get('../../partner_id'));
        }

        $vendorPrices = $vendorPrices->where('min_qty', '<=', $get('product_qty') ?? 1)->where('currency_id', $get('../../currency_id'));

        if (! $vendorPrices->isEmpty()) {
            $vendorPrice = $vendorPrices->first()->price;
        } else {
            $vendorPrice = $product->cost ?? $product->price;
        }

        if (! $get('uom_id')) {
            return $vendorPrice;
        }

        $uom = Uom::find($get('uom_id'));

        return (float) ($vendorPrice / $uom->factor);
    }

    private static function getBestPackaging($productId, $quantity)
    {
        $packagings = Packaging::where('product_id', $productId)
            ->orderByDesc('qty')
            ->get();

        foreach ($packagings as $packaging) {
            if ($quantity && $quantity % $packaging->qty == 0) {
                return [
                    'packaging_id'  => $packaging->id,
                    'packaging_qty' => round($quantity / $packaging->qty, 2),
                ];
            }
        }

        return null;
    }

    private static function calculateLineTotals(Set $set, Get $get, ?string $prefix = ''): void
    {
        if (! $get($prefix.'product_id')) {
            $set($prefix.'price_unit', 0);

            $set($prefix.'discount', 0);

            $set($prefix.'price_tax', 0);

            $set($prefix.'price_subtotal', 0);

            $set($prefix.'price_total', 0);

            return;
        }

        $priceUnit = floatval($get($prefix.'price_unit'));

        $quantity = floatval($get($prefix.'product_qty') ?? 1);

        $subTotal = $priceUnit * $quantity;

        $discountValue = floatval($get($prefix.'discount') ?? 0);

        if ($discountValue > 0) {
            $discountAmount = $subTotal * ($discountValue / 100);

            $subTotal = $subTotal - $discountAmount;
        }

        $taxIds = $get($prefix.'taxes') ?? [];

        [$subTotal, $taxAmount] = TaxFacade::collect($taxIds, $subTotal, $quantity);

        $set($prefix.'price_subtotal', round($subTotal, 4));

        $set($prefix.'price_tax', $taxAmount);

        $set($prefix.'price_total', $subTotal + $taxAmount);
    }
}
