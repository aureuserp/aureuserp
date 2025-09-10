<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\Quotations\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
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
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Field\Filament\Forms\Components\ProgressStepper;
use Webkul\Product\Models\Packaging;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QtyDeliveredMethod;
use Webkul\Sale\Livewire\Summary;
use Webkul\Sale\Models\OrderLine;
use Webkul\Sale\Models\Product;
use Webkul\Sale\Settings\PriceSettings;
use Webkul\Sale\Settings\ProductSettings;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;
use Webkul\Support\Package;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = OrderState::options();

                        if (
                            $record
                            && $record->state != OrderState::CANCEL->value
                        ) {
                            unset($options[OrderState::CANCEL->value]);
                        }

                        if ($record == null) {
                            unset($options[OrderState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(OrderState::DRAFT->value)
                    ->disabled()
                    ->live()
                    ->reactive(),
                Section::make(__('sales::filament/clusters/orders/resources/quotation.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('partner_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.customer'))
                                            ->relationship('partner', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL]))
                                            ->columnSpan(1),
                                    ]),
                                DatePicker::make('validity_date')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.expiration'))
                                    ->native(false)
                                    ->default(fn (QuotationAndOrderSettings $settings) => now()->addDays($settings->default_quotation_validity))
                                    ->required()
                                    ->hidden(fn ($record) => $record)
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                                DatePicker::make('date_order')
                                    ->label(function ($record) {
                                        return $record?->state == OrderState::SALE
                                            ? __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.order-date')
                                            : __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.quotation-date');
                                    })
                                    ->default(now())
                                    ->native(false)
                                    ->required()
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                                Select::make('payment_term_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.payment-term'))
                                    ->relationship('paymentTerm', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(PaymentTerm::find(10)?->id)
                                    ->columnSpan(1),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->schema([
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                self::getProductRepeater(),
                                Livewire::make(Summary::class, function (Get $get, PriceSettings $settings) {
                                    return [
                                        'currency'     => Currency::find($get('currency_id')),
                                        'products'     => $get('products'),
                                        'enableMargin' => $settings->enable_margin,
                                    ];
                                })
                                    ->live()
                                    ->reactive(),
                            ]),
                        Tab::make(__('Optional Products'))
                            ->hidden(fn ($record) => in_array($record?->state, [OrderState::CANCEL]))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->schema(function (Set $set, Get $get) {
                                return [
                                    self::getOptionalProductRepeater($get, $set),
                                ];
                            }),
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.title'))
                                    ->schema([
                                        Select::make('user_id')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.sales-person')),
                                        TextInput::make('client_order_ref')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.customer-reference')),
                                        Select::make('sales_order_tags')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.tags'))
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.title'))
                                    ->schema([
                                        DatePicker::make('commitment_date')
                                            ->disabled(fn ($record) => in_array($record?->state, [OrderState::CANCEL]))
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.fields.commitment-date'))
                                            ->native(false),
                                    ]),
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.title'))
                                    ->schema([
                                        TextInput::make('origin')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.source-document'))
                                            ->maxLength(255),
                                        Select::make('campaign_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.campaign'))
                                            ->relationship('campaign', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('medium_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.medium'))
                                            ->relationship('medium', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('utm_source_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.source'))
                                            ->relationship('utmSource', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.title'))
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.fields.company'))
                                            ->relationship('company', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::user()->default_company_id),
                                        Select::make('currency_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.fields.currency'))
                                            ->relationship('currency', 'name')
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->default(Auth::user()->defaultCompany?->currency_id),
                                    ]),
                            ]),
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('note')
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
            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.title'))
            ->addActionLabel(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->deletable(fn ($record): bool => ! in_array($record?->state, [OrderState::CANCEL]))
            ->addable(fn ($record): bool => ! in_array($record?->state, [OrderState::CANCEL]))
            ->schema([
                Group::make()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('product_id')
                                    ->label(fn (ProductSettings $settings) => $settings->enable_variants ? __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.product-variants') : __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.product-simple'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        function ($query, ProductSettings $settings) {
                                            if (! $settings?->enable_variants) {
                                                return $query->whereNull('parent_id')
                                                    ->where(function ($q) {
                                                        $q->where('is_configurable', true)
                                                            ->orWhere(function ($subq) {
                                                                $subq->whereNull('is_configurable')
                                                                    ->orWhere('is_configurable', false);
                                                            });
                                                    });
                                            }

                                            return $query->withTrashed()->where(function ($q) {
                                                $q->whereNull('parent_id')
                                                    ->orWhereNotNull('parent_id');
                                            });
                                        }
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUpdated($set, $get))
                                    ->required()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])),
                                TextInput::make('product_qty')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.quantity'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->afterStateHydrated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get))
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductQtyUpdated($set, $get))
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])),
                                TextInput::make('qty_delivered')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.qty-delivered'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL]))
                                    ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::SALE])),
                                TextInput::make('qty_invoiced')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.qty-invoiced'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->disabled(true)
                                    ->disabled()
                                    ->visible(fn ($record): bool => in_array($record?->order->state, [OrderState::SALE])),
                                Select::make('product_uom_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.uom'))
                                    ->relationship(
                                        'uom',
                                        'name',
                                        fn ($query) => $query->where('category_id', 1)->orderBy('id'),
                                    )
                                    ->required()
                                    ->live()
                                    ->default(UOM::first()?->id)
                                    ->selectablePlaceholder(false)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterUOMUpdated($set, $get))
                                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])),
                                TextInput::make('customer_lead')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.lead-time'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])),
                                TextInput::make('product_packaging_qty')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.packaging-qty'))
                                    ->live()
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->default(0)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductPackagingQtyUpdated($set, $get))
                                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings),
                                Select::make('product_packaging_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.packaging'))
                                    ->relationship(
                                        'productPackaging',
                                        'name',
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductPackagingUpdated($set, $get))
                                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])),
                                TextInput::make('price_unit')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.unit-price'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])),
                                Hidden::make('purchase_price')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.cost'))
                                    ->default(0),
                                TextInput::make('margin')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.margin'))
                                    ->numeric()
                                    ->default(0)
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->visible(fn (PriceSettings $settings) => $settings->enable_margin)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                                    ->disabled(),
                                TextInput::make('margin_percent')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.margin-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->maxValue(100)
                                    ->live()
                                    ->visible(fn (PriceSettings $settings) => $settings->enable_margin)
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                                    ->disabled(),
                                Select::make('taxes')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.taxes'))
                                    ->relationship(
                                        'taxes',
                                        'name',
                                        fn (Builder $query) => $query->where('type_tax_use', TypeTaxUse::SALE->value),
                                    )
                                    ->searchable()
                                    ->multiple()
                                    ->preload()
                                    ->afterStateHydrated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                                    ->live()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])),
                                TextInput::make('discount')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.discount-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])),
                                TextInput::make('price_subtotal')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.amount'))
                                    ->default(0)
                                    ->readOnly()
                                    ->disabled(fn ($record): bool => $record && $record->order?->locked || in_array($record?->order?->state, [OrderState::CANCEL])),
                                Hidden::make('product_uom_qty')
                                    ->default(0),
                                Hidden::make('price_tax')
                                    ->default(0),
                                Hidden::make('price_total')
                                    ->default(0),
                            ]),
                    ])
                    ->columns(1),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire));
    }

    public static function getOptionalProductRepeater(Get $parentGet, Set $parentSet): Repeater
    {
        return Repeater::make('optionalProducts')
            ->relationship('optionalLines')
            ->hiddenLabel()
            ->live()
            ->reactive()
            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.title'))
            ->addActionLabel(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->schema([
                Group::make()
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Select::make('product_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.product'))
                                    ->relationship(
                                        'product',
                                        'name',
                                        fn ($query) => $query->where('is_configurable', null),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->live()
                                    ->dehydrated()
                                    ->afterStateUpdated(function (Set $set, Get $get) {
                                        $product = Product::find($get('product_id'));

                                        $set('name', $product->name);

                                        $set('price_unit', $product->price);
                                    })
                                    ->required(),
                                TextInput::make('name')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.description'))
                                    ->required()
                                    ->live()
                                    ->dehydrated(),
                                TextInput::make('quantity')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.quantity'))
                                    ->required()
                                    ->default(1)
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->live()
                                    ->dehydrated(),
                                Select::make('product_uom_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.uom'))
                                    ->relationship(
                                        'uom',
                                        'name',
                                        fn ($query) => $query->where('category_id', 1)->orderBy('id'),
                                    )
                                    ->required()
                                    ->live()
                                    ->default(UOM::first()?->id)
                                    ->selectablePlaceholder(false)
                                    ->dehydrated()
                                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom),
                                TextInput::make('price_unit')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.unit-price'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(99999999999)
                                    ->required()
                                    ->live()
                                    ->dehydrated(),
                                TextInput::make('discount')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.discount-percentage'))
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(100)
                                    ->live()
                                    ->dehydrated(),
                                Actions::make([
                                    Action::make('add_order_line')
                                        ->tooltip(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.tooltip.add-order-line'))
                                        ->hiddenLabel()
                                        ->icon('heroicon-o-shopping-cart')
                                        ->action(function ($state, $livewire, $record) use ($parentSet, $parentGet) {
                                            $data = [
                                                'product_id'      => $state['product_id'],
                                                'product_qty'     => $state['quantity'],
                                                'price_unit'      => $state['price_unit'],
                                                'discount'        => $state['discount'],
                                                'name'            => $state['name'],
                                                'customer_lead'   => 0,
                                                'purchase_price'  => 0,
                                                'product_uom_qty' => 0,
                                            ];

                                            $parentSet('products', [
                                                ...$parentGet('products'),
                                                $data,
                                            ]);

                                            $user = Auth::user();

                                            $data['order_id'] = $livewire->record->id;
                                            $data['creator_id'] = $user->id;
                                            $data['company_id'] = $user?->default_company_id;
                                            $data['currency_id'] = $livewire->record->currency_id;
                                            $data['product_uom_id'] = $state['product_uom_id'];
                                            $orderLine = OrderLine::create($data);

                                            $record->line_id = $orderLine->id;

                                            $record->save();

                                            $livewire->refreshFormData(['products']);

                                            $products = collect($parentGet('products'))->values();

                                            $orderLineEntry = $products->first(fn ($product) => $product['id'] == $orderLine->id);

                                            $orderLine->update($orderLineEntry);

                                            Notification::make()
                                                ->success()
                                                ->title(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.product-added.title'))
                                                ->body(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.product-added.body'))
                                                ->send();
                                        })
                                        ->extraAttributes([
                                            'style' => 'margin-top: 2rem;',
                                        ]),
                                ])->hidden(fn ($record) => ! $record ?? false),
                            ]),
                    ])
                    ->columns(2),
            ]);
    }

    public static function mutateProductRelationship(array $data, $record): array
    {
        $product = Product::find($data['product_id']);

        $qtyDeliveredMethod = QtyDeliveredMethod::MANUAL;

        if (Package::isPluginInstalled('inventories')) {
            $qtyDeliveredMethod = QtyDeliveredMethod::STOCK_MOVE;
        }

        return [
            'name'                 => $product->name,
            'qty_delivered_method' => $qtyDeliveredMethod,
            'product_uom_id'       => $data['product_uom_id'] ?? $product->uom_id,
            'currency_id'          => $record->currency_id,
            'partner_id'           => $record->partner_id,
            'creator_id'           => Auth::id(),
            'company_id'           => Auth::user()->default_company_id,
            ...$data,
        ];
    }

    private static function afterProductUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::find($get('product_id'));

        $set('product_uom_id', $product->uom_id);

        $uomQuantity = static::calculateUnitQuantity($get('product_uom_id'), $get('product_qty'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', $product->productTaxes->pluck('id')->toArray());

        $packaging = static::getBestPackaging($get('product_id'), round($uomQuantity, 2));

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        $set('purchase_price', $product->cost ?? 0);

        self::calculateLineTotals($set, $get);
    }

    private static function afterProductQtyUpdated(Set $set, Get $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $uomQuantity = static::calculateUnitQuantity($get('product_uom_id'), $get('product_qty'));

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

        $uomQuantity = static::calculateUnitQuantity($get('product_uom_id'), $get('product_qty'));

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

            $uom = Uom::find($get('product_uom_id'));

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
            $vendorPrice = $product->price ?? $product->cost;
        }

        if (! $get('product_uom_id')) {
            return $vendorPrice;
        }

        $uom = Uom::find($get('product_uom_id'));

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

            $set($prefix.'purchase_price', 0);

            $set($prefix.'margin', 0);

            $set($prefix.'margin_percent', 0);

            return;
        }

        $priceUnit = floatval($get($prefix.'price_unit') ?? 0);

        $quantity = floatval($get($prefix.'product_qty') ?? 1);

        $purchasePrice = floatval($get($prefix.'purchase_price') ?? 0);

        $discountValue = floatval($get($prefix.'discount') ?? 0);

        $subTotal = $priceUnit * $quantity;

        if ($discountValue > 0) {
            $discountAmount = $subTotal * ($discountValue / 100);

            $subTotal -= $discountAmount;
        }

        $taxIds = $get($prefix.'taxes') ?? [];

        [$subTotal, $taxAmount] = Tax::collect($taxIds, $subTotal, $quantity);

        $total = $subTotal + $taxAmount;

        $set($prefix.'price_subtotal', round($subTotal, 4));

        $set($prefix.'price_tax', round($taxAmount, 4));

        $set($prefix.'price_total', round($total, 4));

        [$margin, $marginPercentage] = static::calculateMargin($priceUnit, $purchasePrice, $quantity, $discountValue);

        $set($prefix.'margin', round($margin, 4));

        $set($prefix.'margin_percent', round($marginPercentage, 4));
    }

    public static function calculateMargin($sellingPrice, $costPrice, $quantity, $discount = 0)
    {
        $discountedPrice = $sellingPrice - ($sellingPrice * ($discount / 100));

        $marginPerUnit = $discountedPrice - $costPrice;

        $totalMargin = $marginPerUnit * $quantity;

        if ($marginPerUnit != 0) {
            $marginPercentage = ($marginPerUnit / $discountedPrice) * 100;
        } else {
            $marginPercentage = 0;
        }

        return [
            $totalMargin,
            $marginPercentage,
        ];
    }
}
