<?php

namespace Webkul\PointOfSale\Services;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Account\Enums\AmountType;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\FiscalPositionTax;
use Webkul\Account\Models\Product as AccountProduct;
use Webkul\Account\Models\Tax as TaxModel;
use Webkul\Account\Settings\TaxesSettings;
use Webkul\Inventory\Enums\ProductTracking;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Settings\TraceabilitySettings;
use Webkul\Partner\Enums\AddressType;
use Webkul\Partner\Models\Partner;
use Webkul\PointOfSale\Enums\PriceType;
use Webkul\PointOfSale\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\PointOfSale\Filament\Admin\Pages\Registers;
use Webkul\PointOfSale\Models\Bill;
use Webkul\PointOfSale\Models\Category;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Floor;
use Webkul\PointOfSale\Models\Note;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\PointOfSale\Models\Printer;
use Webkul\PointOfSale\Models\Product;
use Webkul\PointOfSale\Models\Session;
use Webkul\PointOfSale\Models\Table;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\PriceRuleItem;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;
use Webkul\Support\SupportServiceProvider;

class BootLoader
{
    public const PARTNER_LIMIT = 500;

    /**
     * @var array<string, mixed>
     */
    protected array $cache = [];

    public function __construct(
        protected PriceResolver $prices,
    ) {}

    public function load(Config $config, Session $session): array
    {
        $this->cache = [];

        $taxes = $this->collectTaxes($config);

        return [
            'locale'           => $this->locale(),
            'translations'     => $this->translations(),
            'company'          => $this->company($config),
            'config'           => $this->config($config),
            'session'          => $this->session($session),
            'currencies'       => $this->currencies($config),
            'uoms'             => $this->uoms(),
            'categories'       => $this->categories($config),
            'products'         => $this->products($config, $session),
            'variants'         => $this->variants($config, $session),
            'taxes'            => $taxes,
            'fiscal_positions' => $this->fiscalPositions($config),
            'price_lists'      => $this->priceLists($config),
            'prices'           => $this->prices($config, $session),
            'price_tiers'      => $this->priceTiers($config, $session),
            'payment_methods'  => $this->paymentMethods($config),
            'bills'            => $this->bills($config),
            'notes'            => $this->notes(),
            'floors'           => $this->floors($config),
            'partners'         => $this->partners($config),
            'orders'           => $this->openOrders($session),
            'stock'            => $this->stock($config),
            'lots'             => $this->lots($config, $session),
        ];
    }

    protected function locale(): array
    {
        return [
            'code'      => app()->getLocale(),
            'direction' => SupportServiceProvider::isRtl() ? 'rtl' : 'ltr',
        ];
    }

    protected function translations(): array
    {
        $lines = trans('point-of-sale::filament/pos/pages/terminal');

        return is_array($lines) ? $lines : [];
    }

    protected function company(Config $config): array
    {
        $company = $config->company;

        return [
            'id'                              => $company->id,
            'name'                            => $company->name,
            'logo'                            => $company->logo ? Storage::url($company->logo) : null,
            'phone'                           => $company->phone,
            'email'                           => $company->email,
            'website'                         => $company->website,
            'currency_id'                     => $company->currency_id,
            'tax_calculation_rounding_method' => (new TaxesSettings)->tax_calculation_rounding_method,
        ];
    }

    protected function config(Config $config): array
    {
        return [
            'id'                            => $config->id,
            'name'                          => $config->name,
            'code'                          => $config->code,
            'currency_id'                   => $config->currency_id,
            'company_id'                    => $config->company_id,
            'tax_display'                   => $config->tax_display?->value,
            'stock_update_mode'             => Session::defaultStockUpdateMode(),
            'price_list_id'                 => $config->price_list_id,
            'fiscal_position_id'            => $config->usesFiscalPositions() ? $config->fiscal_position_id : null,
            'takeaway_fiscal_position_id'   => $config->takeaway_fiscal_position_id,
            'discount_product_id'           => $config->discount_product_id,
            'tip_product_id'                => $config->tip_product_id,
            'limited_products_amount'       => $config->limited_products_amount,
            'receipt_header'                => $config->receipt_header,
            'receipt_footer'                => $config->receipt_footer,
            'is_restaurant'                 => (bool) $config->is_restaurant,
            'enable_line_discount'          => (bool) $config->enable_line_discount,
            'enable_global_discount'        => (bool) $config->enable_global_discount,
            'global_discount_percentage'    => (float) $config->global_discount_percentage,
            'enable_price_control'          => (bool) $config->enable_price_control,
            'enable_customer_required'      => (bool) $config->enable_customer_required,
            'enable_receipt_print'          => (bool) $config->enable_receipt_print,
            'enable_receipt_auto_print'     => (bool) $config->enable_receipt_auto_print,
            'enable_price_list'             => (bool) $config->enable_price_list,
            'enable_fiscal_position'        => (bool) $config->enable_fiscal_position,
            'enable_tip'                    => (bool) $config->enable_tip,
            'enable_ship_later'             => (bool) $config->enable_ship_later,
            'enable_cash_rounding'          => (bool) $config->enable_cash_rounding,
            'enable_only_round_cash_method' => (bool) $config->enable_only_round_cash_method,
            'enable_split_bill'             => (bool) $config->enable_split_bill,
            'enable_print_bill'             => (bool) $config->enable_print_bill,
            'enable_takeaway'               => (bool) $config->enable_takeaway,
            'show_product_images'           => (bool) $config->show_product_images,
            'show_category_images'          => (bool) $config->show_category_images,
            'show_margins_and_costs'        => (bool) $config->show_margins_and_costs,
            'cash_rounding'                 => $this->cashRounding($config),
            'can_edit_price'                => $this->canEditPrice($config),
            'product_endpoint'              => route('point-of-sale.till.configs.products.store', ['config' => $config->id]),
            'order_cancel_endpoint'         => route('point-of-sale.till.orders.cancel'),
            'draft_endpoint'                => route('point-of-sale.till.orders.drafts.store'),
            'order_refundable_endpoint'     => route('point-of-sale.till.orders.refundable', ['order' => '__order__']),
            'order_refund_endpoint'         => route('point-of-sale.till.orders.refund', ['order' => '__order__']),
            'order_invoice_endpoint'        => route('point-of-sale.till.orders.invoice', ['order' => '__order__']),
            'order_invoice_pdf_endpoint'    => route('point-of-sale.till.orders.invoice-pdf', ['order' => '__order__']),
            'order_receipt_endpoint'        => route('point-of-sale.till.orders.receipt', ['order' => '__order__']),
            'order_details_endpoint'        => OrderResource::getUrl('view', ['record' => '__order__'], panel: 'admin'),
            'floor_plan'                    => $this->floorPlanAccess($config),
            'preparation_printers'          => $this->preparationPrinters($config),
            'cashier_name'                  => Auth::user()?->name,
            'tracking_options'              => $this->trackingOptions(),
            'use_create_lots'               => (bool) $config->operationType?->use_create_lots,
            'use_existing_lots'             => (bool) $config->operationType?->use_existing_lots,
        ];
    }

    protected function preparationPrinters(Config $config): array
    {
        return $config->printers()
            ->with('categories')
            ->orderBy('pos_printers.id')
            ->get()
            ->map(fn (Printer $printer): array => [
                'id'           => $printer->id,
                'name'         => $printer->name,
                'category_ids' => $printer->categories->pluck('id')->all(),
            ])
            ->values()
            ->all();
    }

    protected function trackingOptions(): array
    {
        $isLotsSerialEnabled = settings(TraceabilitySettings::class)->enable_lots_serial_numbers;

        return collect(ProductTracking::cases())
            ->filter(fn (ProductTracking $tracking): bool => $isLotsSerialEnabled || $tracking === ProductTracking::QTY)
            ->map(fn (ProductTracking $tracking): array => [
                'value' => $tracking->value,
                'label' => $tracking->getLabel(),
            ])
            ->values()
            ->all();
    }

    protected function canEditPrice(Config $config): bool
    {
        if (! $config->enable_price_control) {
            return true;
        }

        return (bool) Auth::user()?->can('update', $config);
    }

    protected function cashRounding(Config $config): ?array
    {
        $rounding = $config->cashRounding;

        if (! $config->enable_cash_rounding || ! $rounding) {
            return null;
        }

        return [
            'id'              => $rounding->id,
            'name'            => $rounding->name,
            'rounding'        => (float) $rounding->rounding,
            'rounding_method' => $rounding->rounding_method,
            'strategy'        => $rounding->strategy,
        ];
    }

    protected function session(Session $session): array
    {
        return [
            'id'                          => $session->id,
            'name'                        => $session->name,
            'state'                       => $session->state?->value,
            'config_id'                   => $session->config_id,
            'user_id'                     => $session->user_id,
            'user_name'                   => $session->user?->name,
            'opened_at'                   => $session->started_at?->toIso8601String(),
            'cash_register_balance_start' => (float) $session->cash_balance_start,
            'sequence_number'             => max((int) ($session->order_count ?? 0), (int) Order::withoutGlobalScopes()->where('session_id', $session->id)->max('sequence_number')),
            'login_number'                => (int) ($session->login_number ?? 0),
            'drafts_endpoint'             => route('point-of-sale.till.sessions.drafts', ['session' => $session->id]),
            'orders_endpoint'             => route('point-of-sale.till.sessions.orders', ['session' => $session->id]),
            'opening_control_endpoint'    => route('point-of-sale.till.sessions.opening-control', ['session' => $session->id]),
            'closing_control_endpoint'    => route('point-of-sale.till.sessions.closing-control.report', ['session' => $session->id]),
            'request_closing_endpoint'    => route('point-of-sale.till.sessions.closing-control', ['session' => $session->id]),
            'close_endpoint'              => route('point-of-sale.till.sessions.close', ['session' => $session->id]),
            'cash_movement_endpoint'      => route('point-of-sale.till.sessions.cash-movements', ['session' => $session->id]),
            'cancel_drafts_endpoint'      => route('point-of-sale.till.sessions.cancel-draft-orders', ['session' => $session->id]),
            'sales_details_endpoint'      => route('point-of-sale.till.sessions.sales-details', ['session' => $session->id]),
            'registers_url'               => Registers::getUrl(panel: 'admin'),
            'previous_closing_balance'    => $session->config?->lastClosedSession?->cash_balance_end_real !== null
                ? (float) $session->config->lastClosedSession->cash_balance_end_real
                : null,
            'has_cash_payment_method'     => $session->config?->cashPaymentMethod() !== null,
        ];
    }

    protected function currencies(Config $config): array
    {
        return Currency::query()
            ->whereIn('id', array_filter([$config->currency_id, $config->company?->currency_id]))
            ->get()
            ->map(fn (Currency $currency): array => [
                'id'             => $currency->id,
                'name'           => $currency->name,
                'symbol'         => $currency->symbol,
                'rounding'       => (float) $currency->rounding,
                'decimal_places' => (int) $currency->decimal_places,
                'position'       => $currency->position,
            ])
            ->values()
            ->all();
    }

    protected function uoms(): array
    {
        return UOM::query()
            ->get()
            ->map(fn (UOM $uom): array => [
                'id'       => $uom->id,
                'name'     => $uom->name,
                'rounding' => (float) ($uom->rounding ?? 0.01),
            ])
            ->values()
            ->all();
    }

    protected function categories(Config $config): array
    {
        $query = Category::query()->orderBy('sort');

        if ($categoryIds = $config->availableCategoryIds()) {
            $query->whereIn('id', $categoryIds);
        }

        return $query->get()->map(fn (Category $category): array => [
            'id'        => $category->id,
            'name'      => $category->name,
            'color'     => $category->color,
            'parent_id' => $category->parent_id,
            'image'     => filled($category->image) ? Storage::url($category->image) : null,
        ])->values()->all();
    }

    protected function products(Config $config, ?Session $session = null): array
    {
        $products = $this->sellableProducts($config, $session);

        $hiddenIds = $this->hiddenProductIds($config);

        $missing = array_values(array_diff($hiddenIds, $products->pluck('id')->all()));

        if ($missing) {
            $products = $products->concat(Product::query()->whereKey($missing)->get());
        }

        $priceList = $this->prices->priceListForConfig($config);

        return $products
            ->map(fn (Product $product): array => array_merge(
                $this->productRow($product, $config),
                [
                    'price'     => $this->prices->resolve($product, $priceList),
                    'is_hidden' => in_array($product->id, $hiddenIds, true),
                ],
            ))
            ->values()
            ->all();
    }

    protected function hiddenProductIds(Config $config): array
    {
        return array_values(array_filter([
            $config->enable_tip ? (int) $config->tip_product_id : 0,
            $config->enable_global_discount ? (int) $config->discount_product_id : 0,
        ]));
    }

    protected function variants(Config $config, ?Session $session = null): array
    {
        $parents = $this->configurableParents($config, $session);

        if ($parents->isEmpty()) {
            return [];
        }

        $parents->load([
            'attribute_values.attribute',
            'attribute_values.attributeOption',
            'variants.combinations',
        ]);

        return $parents
            ->map(fn ($parent): array => [
                'product_id' => $parent->id,
                'attributes' => $parent->attribute_values
                    ->groupBy('attribute_id')
                    ->map(fn ($values, $attributeId): array => [
                        'id'     => (int) $attributeId,
                        'name'   => $values->first()->attribute?->name,
                        'values' => $values
                            ->map(fn ($value): array => [
                                'id'          => $value->id,
                                'name'        => $value->attributeOption?->name,
                                'extra_price' => (float) ($value->extra_price ?? 0),
                            ])
                            ->values()
                            ->all(),
                    ])
                    ->values()
                    ->all(),
                'variants' => $parent->variants
                    ->map(fn ($variant): array => [
                        'id'        => $variant->id,
                        'name'      => $variant->name,
                        'value_ids' => $variant->combinations
                            ->pluck('product_attribute_value_id')
                            ->map(fn ($id): int => (int) $id)
                            ->sort()
                            ->values()
                            ->all(),
                    ])
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, Product>
     */
    protected function variantProducts(Config $config, ?Session $session = null): Collection
    {
        return $this->remember('variant-products', function () use ($config, $session): Collection {
            $parentIds = $this->configurableParents($config, $session)->pluck('id');

            if ($parentIds->isEmpty()) {
                return collect();
            }

            return Product::query()
                ->with('posCategories')
                ->whereIn('parent_id', $parentIds)
                ->orderBy('name')
                ->get();
        });
    }

    /**
     * @return Collection<int, Product>
     */
    protected function catalogue(Config $config, ?Session $session = null): Collection
    {
        return $this->remember('catalogue', fn (): Collection => $this->productQuery($config, $session)->get());
    }

    /**
     * @return Collection<int, Product>
     */
    protected function sellableProducts(Config $config, ?Session $session = null): Collection
    {
        return $this->remember('sellable-products', fn (): Collection => $this->catalogue($config, $session)
            ->concat($this->variantProducts($config, $session))
            ->unique('id')
            ->values());
    }

    /**
     * @return Collection<int, Product>
     */
    protected function configurableParents(Config $config, ?Session $session = null): Collection
    {
        return $this->remember('configurable-parents', fn (): Collection => $this->catalogue($config, $session)
            ->where('is_configurable', true)
            ->filter(fn (Product $product): bool => $product->parent_id === null)
            ->values());
    }

    /**
     * @template TValue
     *
     * @param  Closure(): TValue  $resolver
     * @return TValue
     */
    protected function remember(string $key, Closure $resolver)
    {
        return $this->cache[$key] ??= $resolver();
    }

    protected function productRow(Product $product, Config $config): array
    {
        return [
            'id'              => $product->id,
            'name'            => $product->name,
            'reference'       => $product->reference,
            'barcode'         => $product->barcode,
            'uom_id'          => $product->uom_id,
            'price'           => (float) ($product->price ?? 0),
            'cost'            => (float) ($product->cost ?? 0),
            'tax_ids'         => $this->taxIdsFor($product, $config),
            'category_ids'    => $product->posCategories?->pluck('id')->all() ?? [],
            'is_storable'     => (bool) $product->is_storable,
            'tracking'        => $product->tracking?->value ?? ProductTracking::QTY->value,
            'is_configurable' => (bool) $product->is_configurable,
            'parent_id'       => $product->parent_id,
            'image'           => $this->imageUrl($product),
        ];
    }

    protected function imageUrl(Product $product): ?string
    {
        $image = collect($product->images)->first();

        return $image ? Storage::url($image) : null;
    }

    protected function lots(Config $config, ?Session $session = null): array
    {
        if (! $config->operationType?->use_existing_lots) {
            return [];
        }

        $locationIds = $this->stockLocationIds($config);

        if ($locationIds === []) {
            return [];
        }

        $trackedIds = $this->sellableProducts($config, $session)
            ->filter(fn ($product): bool => in_array(
                $product->tracking?->value,
                [ProductTracking::LOT->value, ProductTracking::SERIAL->value],
                true,
            ))
            ->pluck('id');

        if ($trackedIds->isEmpty()) {
            return [];
        }

        return ProductQuantity::withoutGlobalScopes()
            ->whereIn('location_id', $locationIds)
            ->whereIn('product_id', $trackedIds)
            ->whereNotNull('lot_id')
            ->with('lot')
            ->get()
            ->groupBy('lot_id')
            ->map(fn ($rows): array => [
                'id'         => (int) $rows->first()->lot_id,
                'name'       => $rows->first()->lot?->name,
                'product_id' => (int) $rows->first()->product_id,
                'free_qty'   => (float) $rows->sum(fn ($row): float => (float) $row->quantity - (float) $row->reserved_quantity),
            ])
            ->filter(fn (array $lot): bool => filled($lot['name']))
            ->values()
            ->all();
    }

    protected function stock(Config $config): array
    {
        $locationIds = $this->stockLocationIds($config);

        if ($locationIds === []) {
            return [];
        }

        return ProductQuantity::withoutGlobalScopes()
            ->whereIn('location_id', $locationIds)
            ->selectRaw('product_id, SUM(quantity - reserved_quantity) AS free_qty')
            ->groupBy('product_id')
            ->pluck('free_qty', 'product_id')
            ->map(fn ($quantity): float => (float) $quantity)
            ->all();
    }

    protected function stockLocationIds(Config $config): array
    {
        $sourceId = $config->operationType?->source_location_id
            ?? $config->warehouse?->lot_stock_location_id;

        if (! $sourceId) {
            return [];
        }

        $parentPath = Location::withoutGlobalScopes()->whereKey($sourceId)->value('parent_path');

        if (! $parentPath) {
            return [(int) $sourceId];
        }

        return Location::withoutGlobalScopes()
            ->where('parent_path', 'like', $parentPath.'%')
            ->pluck('id')
            ->all();
    }

    public function productPayload(Config $config, int $productId): ?array
    {
        $product = Product::query()->with('posCategories')->find($productId);

        if (! $product) {
            return null;
        }

        return array_merge($this->productRow($product, $config), [
            'resolved_price' => $this->prices->resolve($product, $this->prices->priceListForConfig($config)),
        ]);
    }

    protected function productQuery(Config $config, ?Session $session)
    {
        $referenced = $session
            ? OrderLine::query()
                ->whereIn('order_id', Order::query()
                    ->where('session_id', $session->id)
                    ->where('state', 'draft')
                    ->select('id'))
                ->pluck('product_id')
                ->unique()
                ->filter()
                ->all()
            : [];

        return Product::query()
            ->with('posCategories')
            ->where(fn ($query) => $query
                ->where(fn ($catalogue) => $catalogue
                    ->where('available_in_pos', true)
                    ->whereNull('parent_id')
                    ->when(
                        $config->availableCategoryIds(),
                        fn ($restricted, array $categoryIds) => $restricted->whereHas(
                            'posCategories',
                            fn ($categories) => $categories->whereIn('pos_categories.id', $categoryIds),
                        ),
                    ))
                ->when($referenced, fn ($builder) => $builder->orWhereIn('id', $referenced)))
            ->orderBy('name')
            ->limit(($config->limited_products_amount ?: 500) + count($referenced));
    }

    protected function taxIdsFor(Product $product, Config $config): array
    {
        $accountProduct = AccountProduct::withoutGlobalScopes()->find($product->id);

        if (! $accountProduct) {
            return [];
        }

        return TaxModel::forProduct($accountProduct, TypeTaxUse::SALE, $config->company_id);
    }

    protected function collectTaxes(Config $config): array
    {
        $taxes = TaxModel::query()
            ->withoutGlobalScopes()
            ->where(fn ($query) => $query
                ->where('company_id', $config->company_id)
                ->orWhereNull('company_id'))
            ->where('is_active', true)
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return $taxes->map(fn (TaxModel $tax): array => [
            'id'                  => $tax->id,
            'name'                => $tax->name,
            'invoice_label'       => $tax->invoice_label,
            'sort'                => (int) $tax->sort,
            'amount'              => (float) $tax->amount,
            'amount_type'         => $tax->amount_type?->value,
            'formula'             => $tax->formula,
            'price_include'       => (bool) $tax->price_include,
            'include_base_amount' => (bool) $tax->include_base_amount,
            'is_base_affected'    => (bool) $tax->is_base_affected,
            'has_negative_factor' => (bool) $tax->has_negative_factor,
            'tax_group_id'        => $tax->tax_group_id,
            'children_tax_ids'    => $tax->amount_type === AmountType::GROUP
                ? $tax->childrenTaxes()->orderBy('sort')->orderBy('id')->pluck('accounts_taxes.id')->all()
                : [],
        ])->values()->all();
    }

    protected function fiscalPositions(Config $config): array
    {
        if (! $config->usesFiscalPositions()) {
            return [];
        }

        $positions = $config->enable_fiscal_position ? $config->fiscalPositions : collect();

        foreach ($config->allowedFiscalPositionIds() as $id) {
            if (! $positions->contains('id', $id)) {
                $extra = FiscalPosition::find($id);

                if ($extra) {
                    $positions->push($extra);
                }
            }
        }

        $mappings = FiscalPositionTax::query()
            ->whereIn('fiscal_position_id', $positions->pluck('id'))
            ->get()
            ->groupBy('fiscal_position_id');

        return $positions->map(fn (FiscalPosition $position): array => [
            'id'       => $position->id,
            'name'     => $position->name,
            'tax_map'  => ($mappings[$position->id] ?? collect())
                ->map(fn (FiscalPositionTax $mapping): array => [
                    'source_id'      => $mapping->tax_source_id,
                    'destination_id' => $mapping->tax_destination_id,
                ])->values()->all(),
        ])->values()->all();
    }

    protected function priceLists(Config $config): array
    {
        return $this->availablePriceLists($config)
            ->map(fn (PriceList $priceList): array => [
                'id'          => $priceList->id,
                'name'        => $priceList->name,
                'currency_id' => $priceList->currency_id,
            ])
            ->values()
            ->all();
    }

    /**
     * @return Collection<int, PriceList>
     */
    protected function availablePriceLists(Config $config): Collection
    {
        $priceLists = $config->enable_price_list ? $config->priceLists : collect();

        if ($config->price_list_id && ! $priceLists->contains('id', $config->price_list_id)) {
            $default = $config->priceList ?? PriceList::find($config->price_list_id);

            if ($default) {
                $priceLists = $priceLists->prepend($default);
            }
        }

        return $priceLists->values();
    }

    protected function prices(Config $config, ?Session $session = null): array
    {
        $products = $this->sellableProducts($config, $session);

        $matrix = ['0' => []];

        foreach ($products as $product) {
            $matrix['0'][(string) $product->id] = $this->prices->resolve($product, null);
        }

        foreach ($this->availablePriceLists($config) as $priceList) {
            $matrix[(string) $priceList->id] = [];

            foreach ($products as $product) {
                $matrix[(string) $priceList->id][(string) $product->id] = $this->prices->resolve($product, $priceList);
            }
        }

        return $matrix;
    }

    protected function priceTiers(Config $config, ?Session $session = null): array
    {
        $tiers = [];

        $products = null;

        foreach ($this->availablePriceLists($config) as $priceList) {
            $thresholds = $this->quantityThresholds($priceList);

            if (! $thresholds) {
                continue;
            }

            $products ??= $this->sellableProducts($config, $session);

            foreach ($products as $product) {
                $productTiers = $this->productPriceTiers($product, $priceList, $thresholds);

                if ($productTiers) {
                    $tiers[(string) $priceList->id][(string) $product->id] = $productTiers;
                }
            }
        }

        return $tiers;
    }

    protected function quantityThresholds(PriceList $priceList): array
    {
        $thresholds = [];

        $visited = [$priceList->id];

        $pending = [$priceList->id];

        while ($pending) {
            $items = PriceRuleItem::query()
                ->whereIn('price_list_id', $pending)
                ->get(['min_quantity', 'base_price_list_id']);

            foreach ($items as $item) {
                if (float_compare((float) $item->min_quantity, 1, precisionDigits: 4) > 0) {
                    $thresholds[] = (float) $item->min_quantity;
                }
            }

            $pending = $items->pluck('base_price_list_id')
                ->filter()
                ->unique()
                ->diff($visited)
                ->values()
                ->all();

            $visited = array_merge($visited, $pending);
        }

        $thresholds = array_values(array_unique($thresholds));

        sort($thresholds);

        return $thresholds;
    }

    protected function productPriceTiers(Product $product, PriceList $priceList, array $thresholds): array
    {
        $tiers = [];

        $previous = $this->prices->resolve($product, $priceList);

        foreach ($thresholds as $threshold) {
            $price = $this->prices->resolve($product, $priceList, $threshold);

            if (float_compare($price, $previous, precisionDigits: 4) !== 0) {
                $tiers[] = [
                    'min_qty' => $threshold,
                    'price'   => $price,
                ];
            }

            $previous = $price;
        }

        return $tiers;
    }

    protected function paymentMethods(Config $config): array
    {
        return $config->paymentMethods->sortBy([['sort', 'asc'], ['id', 'asc']])->map(fn (PaymentMethod $method): array => [
            'id'                 => $method->id,
            'name'               => $method->name,
            'type'               => $method->type?->value ?? $method->type,
            'is_cash_count'      => (bool) $method->is_cash_count,
            'split_transactions' => (bool) $method->is_split_transaction,
            'sort'               => (int) $method->sort,
        ])->values()->all();
    }

    protected function bills(Config $config): array
    {
        return Bill::query()
            ->where(fn ($query) => $query
                ->where('is_for_all_configs', true)
                ->orWhereHas('configs', fn ($configs) => $configs->whereKey($config->id)))
            ->orderBy('value')
            ->get()
            ->map(fn (Bill $bill): array => [
                'id'    => $bill->id,
                'name'  => $bill->name,
                'value' => (float) $bill->value,
            ])->values()->all();
    }

    protected function notes(): array
    {
        return Note::query()
            ->orderBy('sort')
            ->get()
            ->map(fn (Note $note): array => [
                'id'    => $note->id,
                'name'  => $note->name,
                'color' => $note->color,
            ])->values()->all();
    }

    protected function partners(Config $config): array
    {
        return Partner::query()
            ->orderBy('name')
            ->limit(static::PARTNER_LIMIT)
            ->withExists(['addresses as has_delivery_address' => fn ($query) => $query->where('sub_type', AddressType::DELIVERY)])
            ->get()
            ->map(fn (Partner $partner): array => [
                'id'                   => $partner->id,
                'name'                 => $partner->name,
                'email'                => $partner->email,
                'phone'                => $partner->phone,
                'mobile'               => $partner->mobile,
                'street1'              => $partner->street1,
                'street2'              => $partner->street2,
                'city'                 => $partner->city,
                'zip'                  => $partner->zip,
                'barcode'              => $partner->barcode ?? null,
                'has_delivery_address' => (bool) $partner->has_delivery_address,
            ])->values()->all();
    }

    protected function floors(Config $config): array
    {
        if (! $config->is_restaurant) {
            return [];
        }

        return $config->floors()
            ->with(['tables' => fn ($query) => $query->orderBy('table_number')])
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->map(fn (Floor $floor): array => $this->floorPayload($floor))
            ->values()
            ->all();
    }

    public function floorPayload(Floor $floor): array
    {
        $floor->loadMissing(['tables' => fn ($query) => $query->orderBy('table_number')]);

        return [
            'id'               => $floor->id,
            'name'             => $floor->name,
            'background_color' => $floor->background_color,
            'background_image' => $floor->background_image ? Storage::url($floor->background_image) : null,
            'tables'           => $floor->tables->map(fn (Table $table): array => [
                'id'           => $table->id,
                'floor_id'     => $table->floor_id,
                'table_number' => $table->table_number,
                'shape'        => $table->shape?->value ?? $table->shape,
                'position_h'   => (float) $table->position_h,
                'position_v'   => (float) $table->position_v,
                'width'        => (float) $table->width,
                'height'       => (float) $table->height,
                'seats'        => (int) $table->seats,
                'color'        => $table->color,
            ])->values()->all(),
        ];
    }

    protected function floorPlanAccess(Config $config): array
    {
        $user = Auth::user();

        return [
            'can_update'          => (bool) $user?->can('update', new Floor),
            'can_create'          => (bool) $user?->can('create', Floor::class),
            'can_delete'          => (bool) $user?->can('delete', new Floor),
            'create_endpoint'     => route('point-of-sale.till.configs.floors.store', ['config' => $config->id]),
            'floor_endpoint'      => route('point-of-sale.till.floors.update', ['floor' => '__floor__']),
            'background_endpoint' => route('point-of-sale.till.floors.background.store', ['floor' => '__floor__']),
        ];
    }

    public function drafts(Session $session): array
    {
        return $this->openOrders($session);
    }

    protected function openOrders(Session $session): array
    {
        return Order::query()
            ->with(['lines.lots', 'lines.taxes', 'payments'])
            ->where('session_id', $session->id)
            ->where('state', 'draft')
            ->get()
            ->map(fn (Order $order): array => [
                'id'                 => $order->id,
                'uuid'               => $order->uuid,
                'pos_reference'      => $order->reference,
                'tracking_number'    => $order->tracking_number,
                'sequence_number'    => $order->sequence_number,
                'partner_id'         => $order->partner_id,
                'state'              => $order->state?->value,
                'note'               => $order->note,
                'is_takeaway'        => (bool) $order->is_takeaway,
                'table_id'           => $order->table_id,
                'floating_name'      => $order->floating_name,
                'is_booked'          => (bool) $order->is_booked,
                'preparation_state'  => $order->preparation_state,
                'customer_count'     => (int) $order->customer_count,
                'price_list_id'      => $order->price_list_id,
                'fiscal_position_id' => $order->fiscal_position_id,
                'to_invoice'         => (bool) $order->is_to_invoice,
                'shipped_at'         => $order->shipped_at?->toDateString(),
                'refunded_order_id'  => $order->refunded_order_id,
                'locked_partner_id'  => $order->refunded_order_id ? $order->partner_id : null,
                'lines'              => $order->lines->map(fn ($line): array => [
                    'uuid'                   => $line->uuid,
                    'product_id'             => $line->product_id,
                    'uom_id'                 => $line->uom_id,
                    'qty'                    => (float) $line->qty,
                    'price_unit'             => (float) $line->price_unit,
                    'price_overridden'       => $line->price_type === PriceType::MANUAL,
                    'discount'               => (float) $line->discount,
                    'note'                   => $line->note,
                    'tax_ids'                => $line->taxes->pluck('id')->all(),
                    'refunded_order_line_id' => $line->refunded_order_line_id,
                    'lots'                   => $line->lots->map(fn ($lot): array => [
                        'lot_name' => $lot->lot_name,
                        'lot_id'   => $lot->lot_id,
                        'qty'      => (float) $lot->qty,
                    ])->values()->all(),
                ])->values()->all(),
                'payments'      => $order->payments->map(fn ($payment): array => [
                    'uuid'              => $payment->uuid,
                    'payment_method_id' => $payment->payment_method_id,
                    'amount'            => (float) $payment->amount,
                    'is_change'         => (bool) $payment->is_change,
                ])->values()->all(),
            ])->values()->all();
    }
}
