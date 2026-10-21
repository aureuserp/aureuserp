<?php

namespace Webkul\PointOfSale\Services;

use Illuminate\Support\Collection;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Account\Models\Product as AccountProduct;
use Webkul\Account\Models\Tax;
use Webkul\PointOfSale\Models\Bill;
use Webkul\PointOfSale\Models\Category;
use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Session;
use Webkul\Product\Models\Product;

class CatalogLoader
{
    public function __construct(
        protected PriceResolver $prices,
    ) {}

    public function load(Config $config, ?string $search = null): array
    {
        return [
            'config'          => $this->config($config),
            'categories'      => $this->categories($config)->all(),
            'products'        => $this->products($config, $search)->all(),
            'payment_methods' => $this->paymentMethods($config)->all(),
            'bills'           => $this->bills($config)->all(),
        ];
    }

    protected function config(Config $config): array
    {
        return [
            'id'                       => $config->id,
            'name'                     => $config->name,
            'code'                     => $config->code,
            'tax_display'              => $config->tax_display,
            'stock_update_mode'        => Session::defaultStockUpdateMode(),
            'enable_line_discount'     => $config->enable_line_discount,
            'enable_price_control'     => $config->enable_price_control,
            'enable_customer_required' => $config->enable_customer_required,
            'enable_ship_later'        => $config->enable_ship_later,
            'enable_tip'               => $config->enable_tip,
            'is_restaurant'            => $config->is_restaurant,
            'enable_split_bill'        => $config->enable_split_bill,
            'show_product_images'      => $config->show_product_images,
            'show_category_images'     => $config->show_category_images,
            'receipt_header'           => $config->receipt_header,
            'receipt_footer'           => $config->receipt_footer,
        ];
    }

    protected function categories(Config $config): Collection
    {
        $query = Category::query()->orderBy('sort');

        if ($config->limit_categories && $config->categories->isNotEmpty()) {
            $query->whereIn('id', $config->categories->pluck('id'));
        }

        return $query->get()->map(fn (Category $category): array => [
            'id'        => $category->id,
            'name'      => $category->name,
            'color'     => $category->color,
            'parent_id' => $category->parent_id,
        ]);
    }

    protected function products(Config $config, ?string $search): Collection
    {
        $query = Product::query()
            ->where('available_in_pos', true)
            ->whereNull('parent_id')
            ->when(
                $config->availableCategoryIds(),
                fn ($builder, array $categoryIds) => $builder->whereHas(
                    'posCategories',
                    fn ($categories) => $categories->whereIn('pos_categories.id', $categoryIds),
                ),
            )
            ->when(filled($search), fn ($builder) => $builder->where('name', 'like', "%{$search}%"))
            ->limit($config->limited_products_amount ?: 500);

        return $query->get()->map(fn (Product $product): array => [
            'id'              => $product->id,
            'name'            => $product->name,
            'reference'       => $product->reference,
            'barcode'         => $product->barcode,
            'uom_id'          => $product->uom_id,
            'price'           => $this->prices->resolve($product, $this->prices->priceListForConfig($config)),
            'tax_ids'         => $this->taxIds($product, $config),
            'is_storable'     => $product->is_storable,
            'is_configurable' => (bool) $product->is_configurable,
        ]);
    }

    protected function taxIds(Product $product, Config $config): array
    {
        $accountProduct = AccountProduct::withoutGlobalScopes()->find($product->id);

        if (! $accountProduct) {
            return [];
        }

        return Tax::forProduct($accountProduct, TypeTaxUse::SALE, $config->company_id);
    }

    protected function paymentMethods(Config $config): Collection
    {
        return $config->paymentMethods->map(fn ($method): array => [
            'id'            => $method->id,
            'name'          => $method->name,
            'type'          => $method->type,
            'is_cash_count' => $method->is_cash_count,
        ]);
    }

    protected function bills(Config $config): Collection
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
                'value' => $bill->value,
            ]);
    }
}
