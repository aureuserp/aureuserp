<?php

namespace Webkul\PointOfSale\Services;

use Webkul\PointOfSale\Models\Config;
use Webkul\PointOfSale\Models\Order;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\Product;
use Webkul\Product\Services\PriceListResolver;
use Webkul\Support\Models\UOM;

class PriceResolver
{
    public function __construct(
        protected PriceListResolver $priceLists,
    ) {}

    public function resolve(Product $product, ?PriceList $priceList = null, float $quantity = 1.0, ?UOM $uom = null): float
    {
        return $this->priceLists->getProductPrice(
            $priceList,
            $product,
            $quantity,
            $uom ?? $product->uom,
            $priceList?->currency,
        );
    }

    public function resolveForOrder(Order $order, Product $product, float $quantity = 1.0, ?UOM $uom = null): float
    {
        return $this->resolve($product, $this->priceListFor($order), $quantity, $uom);
    }

    public function priceListFor(Order $order): ?PriceList
    {
        if ($order->price_list_id) {
            return $order->priceList;
        }

        return $this->priceListForConfig($order->config, $order->partner?->price_list_id);
    }

    public function priceListForConfig(?Config $config, ?int $partnerPriceListId = null): ?PriceList
    {
        if ($partnerPriceListId && $config?->enable_price_list) {
            $partnerPriceList = PriceList::find($partnerPriceListId);

            if ($partnerPriceList) {
                return $partnerPriceList;
            }
        }

        return $config?->priceList;
    }
}
