<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\View\Components\InputComponent\WrapperComponent\IconComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema as DBSchema;
use Illuminate\View\ComponentAttributeBag;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Facades\Tax;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Account\Models\Tax as TaxModel;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Inventory\Models\Product as InventoryProduct;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Inventory\Support\StockScope;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Packaging;
use Webkul\Product\Settings\ProductSettings;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QtyDeliveredMethod;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Sale\Filament\Clusters\Products\Resources\ProductResource;
use Webkul\Sale\Livewire\QuotationSummary;
use Webkul\Sale\Models\Partner;
use Webkul\Sale\Models\Product;
use Webkul\Sale\Settings\PriceSettings;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class QuotationForm
{
    public static function configure(Schema $schema, array $customFormFields = []): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = OrderState::options();

                        if ($record == null) {
                            unset($options[OrderState::CANCEL->value]);
                        } else {
                            if ($record->state !== OrderState::CANCEL) {
                                unset($options[OrderState::CANCEL->value]);
                            } else {
                                unset($options[OrderState::SALE->value]);
                            }
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
                                            ->relationship(
                                                'partner',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('id')->withTrashed()
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->createOptionForm(fn (Schema $schema) => CustomerResource::form($schema))
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $partner = $state ? Partner::find($state) : null;

                                                $set('user_id', $partner?->user?->id);
                                                $set('payment_term_id', $partner?->propertyPaymentTerm?->id);
                                            })
                                            ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::SALE, OrderState::CANCEL]))
                                            ->columnSpan(1)
                                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name.($record->trashed() ? ' (Deleted)' : ''))
                                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)')),
                                    ]),
                                DatePicker::make('validity_date')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.expiration'))
                                    ->native(false)
                                    ->default(fn (QuotationAndOrderSettings $settings) => now()->addDays($settings->default_quotation_validity))
                                    ->required()
                                    ->hidden(fn ($record) => $record)
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::SALE, OrderState::CANCEL])),
                                DatePicker::make('date_order')
                                    ->label(function ($record) {
                                        return $record?->state == OrderState::SALE
                                            ? __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.order-date')
                                            : __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.quotation-date');
                                    })
                                    ->default(now())
                                    ->native(false)
                                    ->required()
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::SALE, OrderState::CANCEL])),
                                Select::make('payment_term_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.payment-term'))
                                    ->relationship(
                                        'paymentTerm',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->schema([
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                static::getProductRepeater(),
                                Livewire::make(QuotationSummary::class, function (Get $get, PriceSettings $settings, $livewire) {
                                    $totals = self::calculateQuotationTotals($get, $livewire);

                                    return [
                                        'currency'         => Currency::find($get('currency_id')),
                                        'enableMargin'     => $settings->enable_margin,
                                        'subtotal'         => $totals['subtotal'],
                                        'totalTax'         => $totals['totalTax'],
                                        'grandTotal'       => $totals['grandTotal'],
                                        'margin'           => $totals['margin'],
                                        'marginPercentage' => $totals['marginPercentage'],
                                    ];
                                })
                                    ->key('quotationSummary')
                                    ->reactive()
                                    ->visible(fn (Get $get) => $get('currency_id') && ! empty($get('products'))),
                            ]),
                        Tab::make(__('Optional Products'))
                            ->hidden(fn ($record) => $record && ! in_array($record->state, [OrderState::DRAFT, OrderState::SENT]))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->schema(function (Set $set, Get $get) {
                                return [
                                    static::getOptionalProductRepeater($get, $set),
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
                                        Select::make('warehouse_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.fields.warehouse'))
                                            ->relationship(
                                                'warehouse',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(
                                                    'company_id',
                                                    $get('company_id') ?? current_company_id(),
                                                )->orderBy('id'),
                                            )
                                            ->default(fn (Get $get): ?int => static::getDefaultWarehouseId(
                                                $get('company_id') ?? current_company_id()
                                            ))
                                            ->searchable()
                                            ->preload()
                                            ->disabled(fn ($record) => in_array($record?->state, [OrderState::SALE, OrderState::CANCEL]))
                                            ->live()
                                            ->visible(fn (): bool => static::canUseInventoryWarehouses()),
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
                                            ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get, ?int $state): void {
                                                $companyId = $state ?? current_company_id();

                                                $set('currency_id', Company::find($state)?->currency_id);
                                                $set('warehouse_id', static::getDefaultWarehouseId($companyId));

                                                clear_foreign_company_values($set, $get, [
                                                    'payment_term_id' => PaymentTerm::class,
                                                ], $companyId);
                                            })
                                            ->reactive()
                                            ->default(current_company_id()),
                                        Select::make('currency_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.fields.currency'))
                                            ->relationship(
                                                name: 'currency',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->active(),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->reactive()
                                            ->default(current_company()?->currency_id),
                                        ...$customFormFields,
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

    private static function canUseInventoryWarehouses(): bool
    {
        return Package::isPluginInstalled('inventories')
            && DBSchema::hasTable('inventories_warehouses');
    }

    private static function getDefaultWarehouseId(?int $companyId): ?int
    {
        if (! $companyId || ! static::canUseInventoryWarehouses()) {
            return null;
        }

        return Warehouse::where('company_id', $companyId)->orderBy('id')->value('id');
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
            ->deleteAction(function (Action $action) {
                $action->requiresConfirmation();

                $action->before(function (Action $action, $livewire) {
                    $arguments = $action->getArguments();

                    if (
                        ! empty($arguments['item'] ?? '') &&
                        ! str_starts_with($arguments['item'] ?? '', 'record-')
                    ) {
                        return;
                    }

                    if ($livewire->getRecord()?->state === OrderState::SALE) {
                        Notification::make()
                            ->danger()
                            ->title(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.delete-action.error.title'))
                            ->body(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.delete-action.error.body'))
                            ->send();

                        $action->cancel();

                        return;
                    }
                });

                $action->after(function (Get $get, $livewire) {
                    $totals = self::calculateQuotationTotals($get, $livewire);

                    $livewire->dispatch('itemUpdated', $totals);
                });
            })
            ->addable(fn ($record): bool => ! in_array($record?->state, [OrderState::CANCEL]))
            ->compact()
            ->table(fn ($record) => [
                TableColumn::make('product_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.product'))
                    ->resizable()
                    ->wrapHeader(false)
                    ->width(300)
                    ->markAsRequired()
                    ->toggleable(),
                TableColumn::make('product_qty')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.quantity'))
                    ->markAsRequired()
                    ->resizable()
                    ->wrapHeader(false),
                TableColumn::make('qty_delivered')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.qty-delivered'))
                    ->toggleable()
                    ->markAsRequired()
                    ->resizable()
                    ->wrapHeader(false)
                    ->visible(fn () => in_array($record?->state, [OrderState::SALE])),
                TableColumn::make('qty_invoiced')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.qty-invoiced'))
                    ->markAsRequired()
                    ->toggleable()
                    ->resizable()
                    ->wrapHeader(false)
                    ->visible(fn () => in_array($record?->state, [OrderState::SALE])),
                TableColumn::make('product_uom_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.uom'))
                    ->toggleable()
                    ->markAsRequired()
                    ->resizable()
                    ->wrapHeader(false)
                    ->visible(fn () => settings(ProductSettings::class)->enable_uom),
                TableColumn::make('customer_lead')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.lead-time'))
                    ->markAsRequired()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable()
                    ->wrapHeader(false),
                TableColumn::make('product_packaging_qty')
                    ->toggleable()
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.packaging-qty'))
                    ->resizable()
                    ->wrapHeader(false)
                    ->visible(fn () => settings(ProductSettings::class)->enable_packagings),
                TableColumn::make('product_packaging_id')
                    ->toggleable()
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.packaging'))
                    ->resizable()
                    ->wrapHeader(false)
                    ->visible(fn () => settings(ProductSettings::class)->enable_packagings),
                TableColumn::make('price_unit')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.unit-price'))
                    ->markAsRequired()
                    ->resizable()
                    ->wrapHeader(false),
                TableColumn::make('margin')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.margin'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable()
                    ->wrapHeader(false)
                    ->visible(fn () => settings(PriceSettings::class)->enable_margin),
                TableColumn::make('margin_percent')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.margin-percentage'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable()
                    ->wrapHeader(false)
                    ->visible(fn () => settings(PriceSettings::class)->enable_margin),
                TableColumn::make('taxes')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.taxes'))
                    ->toggleable()
                    ->resizable()
                    ->wrapHeader(false),
                TableColumn::make('discount')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.discount-percentage'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable()
                    ->wrapHeader(false)
                    ->visible(fn () => settings(PriceSettings::class)->enable_discount),
                TableColumn::make('price_subtotal')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.amount'))
                    ->toggleable()
                    ->resizable()
                    ->wrapHeader(false),
            ])
            ->schema(fn ($record) => [
                Select::make('product_id')
                    ->label(fn (ProductSettings $settings) => $settings->enable_variants ? __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.product-variants') : __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.product-simple'))
                    ->relationship(
                        name: 'product',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                            ->withTrashed()
                            ->whereNull('is_configurable')
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->selectablePlaceholder(false)
                    ->searchable()
                    ->preload()
                    ->live()
                    ->required()
                    ->dehydrated(true)
                    ->wrapOptionLabels(false)
                    ->disabled(fn (Get $get): bool => filled($get('id')) && in_array($record?->state, [OrderState::SALE, OrderState::CANCEL]))
                    ->disableOptionWhen(function ($label, $value, $state, $component) {
                        $isDeleted = str_contains($label, ' (Deleted)');

                        if ($isDeleted) {
                            return true;
                        }

                        $isDuplicate = false;

                        if ($component?->getParentRepeater()) {
                            $repeater = $component->getParentRepeater();

                            $isDuplicate = collect($repeater->getState())
                                ->pluck(
                                    (string) str($component->getStatePath())
                                        ->after("{$repeater->getStatePath()}.")
                                        ->after('.'),
                                )
                                ->flatten()
                                ->diff(Arr::wrap($state))
                                ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                                ->contains($value);
                        }

                        return $isDuplicate;
                    })
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductUpdated($set, $get)),
                TextInput::make('product_qty')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->suffix(function ($record, Get $get): mixed {
                        if (! Package::isPluginInstalled('inventories')) {
                            return null;
                        }

                        if ($record && $record?->state !== OrderState::DRAFT) {
                            return null;
                        }

                        $productId = $get('product_id');

                        if (! $productId) {
                            return null;
                        }

                        $requestedQty = (float) ($get('product_qty') ?? 0);

                        if (
                            $requestedQty <= 0
                            || (float) ($get('qty_delivered') ?? 0) > 0
                        ) {
                            return null;
                        }

                        $inventoryProduct = InventoryProduct::query()->withTrashed()->find($productId);

                        if (! $inventoryProduct) {
                            return null;
                        }

                        if (! $inventoryProduct->is_storable) {
                            return null;
                        }

                        $warehouseId = $get('../../warehouse_id');

                        $companyId = $get('../../company_id') ?? current_company_id();

                        $inventoryProduct->withStockScope(
                            StockScope::make()
                                ->forWarehouses(filled($warehouseId) ? (int) $warehouseId : null)
                                ->forCompanies(filled($companyId) ? (int) $companyId : null)
                        );

                        $freeQty = (float) $inventoryProduct->free_qty;

                        if ($requestedQty <= $freeQty) {
                            return null;
                        }

                        return \Filament\Support\generate_icon_html(
                            'heroicon-o-exclamation-triangle',
                            null,
                            (new ComponentAttributeBag)
                                ->color(IconComponent::class, 'warning')
                                ->class(['fi-text-color-600'])
                                ->merge([
                                    'style'         => 'color: var(--text)',
                                    'x-tooltip.raw' => __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.columns.insufficient-stock-tooltip'),
                                ], escape: false),
                        );
                    })
                    ->rule(function (Get $get): \Closure {
                        return function (string $attribute, $value, \Closure $fail) use ($get): void {
                            $qtyDelivered = (float) ($get('qty_delivered') ?? 0);

                            if ($qtyDelivered > 0 && (float) $value < $qtyDelivered) {
                                $fail(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.notifications.quantity-below-delivered.body', ['qty' => $qtyDelivered]));
                            }
                        };
                    })
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        static::afterProductQtyUpdated($set, $get);
                    })
                    ->disabled(fn (): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                TextInput::make('qty_delivered')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.qty-delivered'))
                    ->required()
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->disabled(fn (Get $get, $record): bool => (! filled($get('id')) && in_array($get('../../state'), [OrderState::SALE->value, OrderState::SALE])) || $record?->order->locked || in_array($record?->order->state, [OrderState::CANCEL]) || $record?->qty_delivered_method == QtyDeliveredMethod::STOCK_MOVE)
                    ->visible(fn (): bool => in_array($record?->state, [OrderState::SALE])),
                TextInput::make('qty_invoiced')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.qty-invoiced'))
                    ->required()
                    ->default(0)
                    ->numeric()
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->disabled()
                    ->visible(fn (): bool => in_array($record?->state, [OrderState::SALE])),
                Select::make('product_uom_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.uom'))
                    ->wrapOptionLabels(false)
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get) {
                            $product = Product::withTrashed()->find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))->orderBy('id');
                        },
                    )
                    ->required()
                    ->live()
                    ->native(false)
                    ->selectablePlaceholder(false)
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterUOMUpdated($set, $get))
                    ->visible(fn (ProductSettings $settings) => $settings->enable_uom)
                    ->disabled(fn (Get $get): bool => filled($get('id')) && ($record?->locked || in_array($record?->state, [OrderState::SALE, OrderState::CANCEL]))),
                TextInput::make('customer_lead')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.lead-time'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->disabled(fn (): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                TextInput::make('product_packaging_qty')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.packaging-qty'))
                    ->live(onBlur: true)
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->default(0)
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductPackagingQtyUpdated($set, $get))
                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                    ->disabled(fn (): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                Select::make('product_packaging_id')
                    ->wrapOptionLabels(false)
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.packaging'))
                    ->relationship(
                        'productPackaging',
                        'name',
                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('product_id', $get('product_id')),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, Get $get) => static::afterProductPackagingUpdated($set, $get))
                    ->visible(fn (ProductSettings $settings) => $settings->enable_packagings)
                    ->disableOptionWhen(fn ($value): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                TextInput::make('price_unit')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.unit-price'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                    ->disabled(fn (): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                TextInput::make('margin')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.margin'))
                    ->numeric()
                    ->default(0)
                    ->maxValue(99999999999)
                    ->visible(fn (PriceSettings $settings) => $settings->enable_margin)
                    ->readonly(),
                TextInput::make('margin_percent')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.margin-percentage'))
                    ->numeric()
                    ->default(0)
                    ->maxValue(100)
                    ->visible(fn (PriceSettings $settings) => $settings->enable_margin)
                    ->readonly(),
                Select::make('taxes')
                    ->wrapOptionLabels(false)
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.taxes'))
                    ->relationship(
                        'taxes',
                        'name',
                        fn (Builder $query, Get $get) => $query
                            ->where('type_tax_use', TypeTaxUse::SALE)
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->searchable()
                    ->multiple()
                    ->preload()
                    ->afterStateUpdated(fn (Get $get, Set $set) => self::calculateLineTotals($set, $get))
                    ->live()
                    ->disableOptionWhen(fn ($value): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                TextInput::make('discount')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->visible(fn (PriceSettings $settings) => $settings->enable_discount)
                    ->afterStateUpdated(fn (Set $set, Get $get) => self::calculateLineTotals($set, $get))
                    ->disabled(fn (): bool => $record?->locked || in_array($record?->state, [OrderState::CANCEL])),
                TextInput::make('price_subtotal')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.amount'))
                    ->default(0)
                    ->disabled(),
                Hidden::make('product_uom_qty')
                    ->default(0),
                Hidden::make('price_tax')
                    ->default(0),
                Hidden::make('price_total')
                    ->default(0),
                Hidden::make('purchase_price')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.fields.cost'))
                    ->default(0),
            ])
            ->mutateRelationshipDataBeforeCreateUsing(fn (array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire))
            ->mutateRelationshipDataBeforeSaveUsing(fn (array $data, $record, $livewire) => static::mutateProductRelationship($data, $record, $livewire))
            ->extraItemActions([
                Action::make('openProduct')
                    ->tooltip(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.products.actions.open-product.tooltip'))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(
                        fn (array $arguments, Get $get): ?string => ProductResource::getUrl('edit', [
                            'record' => $get("products.{$arguments['item']}.product_id"),
                        ])
                    )
                    ->openUrlInNewTab()
                    ->visible(
                        fn (array $arguments, Get $get): bool => filled($get("products.{$arguments['item']}.product_id"))
                    ),
            ]);
    }

    public static function getOptionalProductRepeater(Get $parentGet, Set $parentSet): Repeater
    {
        $isPresent = function (array $arguments, Get $get) use ($parentGet): bool {
            $productId = $get("optionalProducts.{$arguments['item']}.product_id");

            if (! filled($productId)) {
                return false;
            }

            return collect($parentGet('products') ?? [])
                ->contains(fn ($product) => ($product['product_id'] ?? null) == $productId);
        };

        return Repeater::make('optionalProducts')
            ->relationship('optionalLines')
            ->hiddenLabel()
            ->live()
            ->compact()
            ->reactive()
            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.title'))
            ->addActionLabel(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.add-product'))
            ->collapsible()
            ->defaultItems(0)
            ->itemLabel(function ($state) {
                if (! empty($state['name'])) {
                    return $state['name'];
                }

                $product = Product::find($state['product_id']);

                return $product->name ?? null;
            })
            ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
            ->table([
                TableColumn::make('product_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.product'))
                    ->width(300)
                    ->markAsRequired()
                    ->toggleable()
                    ->resizable(),
                TableColumn::make('quantity')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.quantity'))
                    ->markAsRequired()
                    ->resizable(),
                TableColumn::make('product_uom_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.uom'))
                    ->markAsRequired()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->resizable(),
                TableColumn::make('price_unit')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.unit-price'))
                    ->markAsRequired()
                    ->resizable()
                    ->wrapHeader(false),
                TableColumn::make('discount')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.columns.discount-percentage'))
                    ->toggleable()
                    ->resizable()
                    ->wrapHeader(false),
            ])
            ->schema([
                Select::make('product_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.product'))
                    ->relationship(
                        'product',
                        'name',
                        fn (Builder $query, Get $get) => $query
                            ->withTrashed()
                            ->where('is_configurable', null)
                            ->where(owned_by_company($get('../../company_id'))),
                    )
                    ->searchable()
                    ->preload()
                    ->live()
                    ->dehydrated(true)
                    ->wrapOptionLabels(false)
                    ->getOptionLabelFromRecordUsing(function ($record): string {
                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                    })
                    ->disableOptionWhen(function ($value, $state, $component, $label) {
                        if (str_contains($label, ' (Deleted)')) {
                            return true;
                        }

                        $repeater = $component->getParentRepeater();
                        if (! $repeater) {
                            return false;
                        }

                        return collect($repeater->getState())
                            ->pluck(
                                (string) str($component->getStatePath())
                                    ->after("{$repeater->getStatePath()}.")
                                    ->after('.'),
                            )
                            ->flatten()
                            ->diff(Arr::wrap($state))
                            ->filter(fn (mixed $siblingItemState): bool => filled($siblingItemState))
                            ->contains($value);
                    })
                    ->afterStateUpdated(function (Set $set, Get $get) {
                        if (! $get('product_id')) {
                            return;
                        }

                        $product = Product::withTrashed()->find($get('product_id'));

                        $set('name', $product->name);
                        $set('price_unit', $product->price);
                        $set('product_uom_id', $product->uom_id);
                    })
                    ->required(),
                Hidden::make('name')
                    ->dehydrated(),
                TextInput::make('quantity')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.quantity'))
                    ->required()
                    ->default(1)
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(99999999999)
                    ->live(onBlur: true)
                    ->dehydrated(),
                Select::make('product_uom_id')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.uom'))
                    ->relationship(
                        'uom',
                        'name',
                        function (Builder $query, Get $get) {
                            $product = Product::withTrashed()->find($get('product_id'));
                            $categoryId = $product?->uom?->category_id;

                            return $query->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))->orderBy('id');
                        },
                    )
                    ->required()
                    ->wrapOptionLabels(false)
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
                    ->live(onBlur: true)
                    ->dehydrated(),
                TextInput::make('discount')
                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.discount-percentage'))
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100)
                    ->live(onBlur: true)
                    ->visible(fn (PriceSettings $settings) => $settings->enable_discount)
                    ->dehydrated(),
            ])
            ->extraItemActions([
                Action::make('add_order_line')
                    ->tooltip(fn (array $arguments, Get $get): string => $isPresent($arguments, $get)
                        ? __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.tooltip.already-added')
                        : __('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.tooltip.add-order-line'))
                    ->hiddenLabel()
                    ->icon(fn (array $arguments, Get $get): string => $isPresent($arguments, $get)
                        ? 'heroicon-o-check-circle'
                        : 'heroicon-o-shopping-cart')
                    ->color(fn (array $arguments, Get $get): string => $isPresent($arguments, $get)
                        ? 'success'
                        : 'gray')
                    ->disabled(fn (array $arguments, Get $get): bool => $isPresent($arguments, $get))
                    ->action(function ($state, $livewire, $record, $arguments) use ($parentGet, $parentSet) {
                        $uuid = $arguments['item'];
                        $productData = $state[$uuid] ?? null;

                        if (! $productData || ! $productData['product_id']) {
                            Notification::make()
                                ->danger()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.missing-product-data.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.missing-product-data.body'))
                                ->send();

                            return;
                        }

                        $existingProducts = $parentGet('products') ?? [];
                        $productExists = collect($existingProducts)->contains(function ($product) use ($productData) {
                            return ($product['product_id'] ?? null) == $productData['product_id'];
                        });

                        if ($productExists) {
                            Notification::make()
                                ->warning()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.product-already-exists.title'))
                                ->body(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.product-already-exists.body'))
                                ->send();

                            return;
                        }

                        $product = Product::withTrashed()->find($productData['product_id']);

                        if (! $product) {
                            Notification::make()
                                ->danger()
                                ->title(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.product-not-found.title'))
                                ->send();

                            return;
                        }

                        $newLineData = [
                            'product_id'            => $productData['product_id'],
                            'product_qty'           => $productData['quantity'] ?? 1,
                            'price_unit'            => $productData['price_unit'] ?? 0,
                            'discount'              => $productData['discount'] ?? 0,
                            'name'                  => $productData['name'] ?? $product->name,
                            'product_uom_id'        => $productData['product_uom_id'] ?? $product->uom_id,
                            'customer_lead'         => 0,
                            'purchase_price'        => $product->cost ?? 0,
                            'product_uom_qty'       => 0,
                            'price_subtotal'        => 0,
                            'price_tax'             => 0,
                            'price_total'           => 0,
                            'margin'                => 0,
                            'margin_percent'        => 0,
                            'taxes'                 => TaxModel::forProduct($product, TypeTaxUse::SALE),
                            'product_packaging_id'  => null,
                            'product_packaging_qty' => null,
                        ];

                        $tempState = $newLineData;

                        $tempSet = function ($key, $value) use (&$tempState) {
                            $tempState[$key] = $value;
                        };

                        $tempGet = function ($key) use (&$tempState, $parentGet) {
                            if (str_starts_with($key, '../../')) {
                                $parentKey = str_replace('../../', '', $key);

                                return $parentGet($parentKey);
                            }

                            return $tempState[$key] ?? null;
                        };

                        static::afterProductUpdated($tempSet, $tempGet);

                        $currentProducts = $parentGet('products') ?? [];
                        $parentSet('products', [...$currentProducts, $tempState]);

                        Notification::make()
                            ->success()
                            ->title(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.product-added.title'))
                            ->body(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.repeater.product-optional.fields.actions.notifications.product-added.body'))
                            ->send();
                    })
                    ->visible(
                        fn (array $arguments, Get $get, $record): bool => filled($record)
                            && filled($get("optionalProducts.{$arguments['item']}.product_id"))
                    ),
            ]);
    }

    public static function mutateProductRelationship(array $data, $record): array
    {
        $product = Product::withTrashed()->find($data['product_id']);

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
            'company_id'           => $record->company_id,
            ...$data,
        ];
    }

    private static function afterProductUpdated($set, $get): void
    {
        if (! $get('product_id')) {
            return;
        }

        $product = Product::withTrashed()->find($get('product_id'));

        $set('product_uom_id', $product->uom_id);

        $uomQuantity = static::calculateUnitQuantity($product->uom_id, $get('product_qty'));

        $set('product_uom_qty', round($uomQuantity, 2));

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        $set('taxes', TaxModel::forProduct($product, TypeTaxUse::SALE, $get('../../company_id')));

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

        $product = Product::withTrashed()->find($get('product_id'));

        $uomQuantity = static::calculateUnitQuantity($get('product_uom_id'), $get('product_qty'), $product->uom_id);

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

        $product = Product::withTrashed()->find($get('product_id'));

        $uomQuantity = static::calculateUnitQuantity($get('product_uom_id'), $get('product_qty'), $product->uom_id);

        $set('product_uom_qty', round($uomQuantity, 2));

        $packaging = static::getBestPackaging($get('product_id'), $uomQuantity);

        $set('product_packaging_id', $packaging['packaging_id'] ?? null);

        $set('product_packaging_qty', $packaging['packaging_qty'] ?? null);

        $priceUnit = static::calculateUnitPrice($get);

        $set('price_unit', round($priceUnit, 2));

        $purchasePrice = static::calculatePurchasePrice($product, $get('product_uom_id'));

        $set('purchase_price', round($purchasePrice, 2));

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

            $uom = UOM::find($get('product_uom_id'));

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

    private static function calculateUnitQuantity($fromUomId, $quantity, $toUomId = null): float
    {
        if (! $fromUomId || ! filled($quantity)) {
            return (float) ($quantity ?? 0);
        }

        $fromUom = UOM::find($fromUomId);

        if (! $fromUom) {
            return (float) ($quantity ?? 0);
        }

        $toUom = $toUomId ? UOM::find($toUomId) : $fromUom;

        if (! $toUom) {
            return (float) ($quantity ?? 0);
        }

        return $fromUom->computeQuantity((float) ($quantity ?? 0), $toUom, false);
    }

    private static function calculateUnitPrice($get)
    {
        $product = Product::withTrashed()->find($get('product_id'));

        $vendorPrices = $product->sellers->sortByDesc('sort');

        if ($get('../../partner_id')) {
            $vendorPrices = $vendorPrices->where('partner_id', $get('../../partner_id'));
        }

        $vendorPrices = $vendorPrices->where('min_qty', '<=', $get('product_qty') ?? 1)->where('currency_id', $get('../../currency_id'));

        if (! $vendorPrices->isEmpty()) {
            $vendorPrice = $vendorPrices->first()->price;
        } else {
            $vendorPrice = $product->price ?? $product->cost;
        }

        if (! $get('product_uom_id') || ! $product->uom) {
            return $vendorPrice;
        }

        $uomQty = UOM::find($get('product_uom_id'))->computeQuantity(1, $product->uom, false);

        return (float) ($vendorPrice * $uomQty);
    }

    private static function calculatePurchasePrice($product, $uomId): float
    {
        $cost = (float) ($product->cost ?? 0);

        if (! $uomId || ! $product->uom) {
            return $cost;
        }

        $uomQty = UOM::find($uomId)->computeQuantity(1, $product->uom, false);

        return $cost * $uomQty;
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

    private static function calculateLineTotals($set, $get, ?string $prefix = ''): void
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

        $discountedUnit = $discountValue > 0 ? $priceUnit * (1 - ($discountValue / 100)) : $priceUnit;

        $taxIds = $get($prefix.'taxes') ?? [];

        $taxes = TaxModel::whereIn('id', $taxIds)->get();

        if ($taxes->isEmpty()) {
            $subTotal = round($discountedUnit * $quantity, 4);

            $set($prefix.'price_subtotal', $subTotal);

            $set($prefix.'price_tax', 0);

            $set($prefix.'price_total', $subTotal);
        } else {
            $taxResult = Tax::computeAll($taxes, $discountedUnit, null, $quantity);

            $set($prefix.'price_subtotal', round($taxResult['total_excluded'], 4));

            $set($prefix.'price_tax', round($taxResult['total_included'] - $taxResult['total_excluded'], 4));

            $set($prefix.'price_total', round($taxResult['total_included'], 4));
        }

        [$margin, $marginPercentage] = static::calculateMargin($priceUnit, $purchasePrice, $quantity, $discountValue);

        $set($prefix.'margin', round($margin, 4));

        $set($prefix.'margin_percent', round($marginPercentage, 4));
    }

    private static function calculateQuotationTotals(Get $get, $livewire): array
    {
        $defaultTotals = [
            'subtotal'         => 0,
            'totalTax'         => 0,
            'grandTotal'       => 0,
            'margin'           => 0,
            'marginPercentage' => 0,
        ];

        $products = $get('products') ?? [];

        if (empty($products)) {
            $livewire->dispatch('itemUpdated', $defaultTotals);

            return $defaultTotals;
        }

        $subtotal = 0;
        $totalTax = 0;
        $grandTotal = 0;
        $margin = 0;

        foreach ($products as $product) {
            if (empty($product['product_id'])) {
                continue;
            }

            $subtotal += floatval($product['price_subtotal'] ?? 0);
            $totalTax += floatval($product['price_tax'] ?? 0);
            $grandTotal += floatval($product['price_total'] ?? 0);
            $margin += floatval($product['margin'] ?? 0);
        }

        $marginPercentage = ($subtotal > 0) ? ($margin / $subtotal) * 100 : 0;

        $totals = [
            'subtotal'         => round($subtotal, 2),
            'totalTax'         => round($totalTax, 2),
            'grandTotal'       => round($grandTotal, 2),
            'margin'           => round($margin, 2),
            'marginPercentage' => round($marginPercentage, 2),
        ];

        $livewire->dispatch('itemUpdated', $totals);

        return $totals;
    }

    public static function calculateMargin($sellingPrice, $costPrice, $quantity, $discount = 0)
    {
        $discountedPrice = $sellingPrice - ($sellingPrice * ($discount / 100));

        $marginPerUnit = $discountedPrice - $costPrice;

        $totalMargin = $marginPerUnit * $quantity;

        if ($marginPerUnit != 0 && $discountedPrice != 0) {
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
