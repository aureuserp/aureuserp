<?php

namespace Webkul\Product\Services;

use DateTimeInterface;
use Illuminate\Support\Collection;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\PriceRuleItem;
use Webkul\Product\Models\Product;
use Webkul\Product\Support\ResolvedPrice;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

class PriceListResolver
{
    private const MAX_BASE_DEPTH = 5;

    public function getProductPrice(
        ?PriceList $priceList,
        Product $product,
        float $quantity = 1.0,
        ?UOM $uom = null,
        ?Currency $currency = null,
        ?DateTimeInterface $date = null,
        ?Company $company = null,
    ): float {
        return $this->resolve($priceList, $product, $quantity, $uom, $currency, $date, $company)->price;
    }

    public function getProductPriceRule(
        ?PriceList $priceList,
        Product $product,
        float $quantity = 1.0,
        ?UOM $uom = null,
        ?DateTimeInterface $date = null,
    ): ?PriceRuleItem {
        if (! $priceList) {
            return null;
        }

        $date ??= now();

        return $this->findRule(
            $priceList,
            $product,
            $this->toProductQuantity($product, $quantity, $uom),
            $date,
        );
    }

    public function resolve(
        ?PriceList $priceList,
        Product $product,
        float $quantity = 1.0,
        ?UOM $uom = null,
        ?Currency $currency = null,
        ?DateTimeInterface $date = null,
        ?Company $company = null,
    ): ResolvedPrice {
        $date ??= now();

        $company ??= current_company();

        $targetUom = $uom ?? $product->uom;

        $currency ??= $priceList?->currency ?? $this->defaultCurrency();

        return $this->compute($priceList, $product, $quantity, $targetUom, $currency, $date, $company, 0, []);
    }

    private function compute(
        ?PriceList $priceList,
        Product $product,
        float $quantity,
        ?UOM $targetUom,
        ?Currency $currency,
        DateTimeInterface $date,
        ?Company $company,
        int $depth,
        array $visited,
    ): ResolvedPrice {
        $quantityInProductUom = $this->toProductQuantity($product, $quantity, $targetUom);

        $rule = $priceList
            ? $this->findRule($priceList, $product, $quantityInProductUom, $date)
            : null;

        if (! $rule) {
            $salesPrice = $this->productPrice($product, PriceRuleBase::LIST_PRICE, $targetUom, $currency, $date, $company);

            return new ResolvedPrice($salesPrice, null, $salesPrice);
        }

        $basePrice = $this->basePriceFor(
            $rule->base ?? PriceRuleBase::LIST_PRICE,
            $rule->basePriceList,
            $product,
            $quantity,
            $targetUom,
            $currency,
            $date,
            $company,
            $depth,
            $priceList ? [...$visited, $priceList->id] : $visited,
        );

        $price = match ($rule->type) {
            PriceRuleType::FIXED      => $this->convertRuleAmount($rule, $rule->fixed_price, $product, $targetUom, $currency, $date, $company),
            PriceRuleType::PERCENTAGE => $basePrice - ($basePrice * ($rule->percent_price / 100)),
            PriceRuleType::FORMULA    => $this->applyFormula($rule, $basePrice, $product, $targetUom, $currency, $date, $company),
            default                   => $basePrice,
        };

        return new ResolvedPrice($price, $rule, $basePrice);
    }

    private function applyFormula(
        PriceRuleItem $rule,
        float $basePrice,
        Product $product,
        ?UOM $targetUom,
        ?Currency $currency,
        DateTimeInterface $date,
        ?Company $company,
    ): float {
        $priceLimit = $basePrice;

        $discount = $rule->base === PriceRuleBase::STANDARD_PRICE
            ? -$rule->price_markup
            : $rule->price_discount;

        $price = $basePrice - ($basePrice * ($discount / 100));

        if ($rule->price_round) {
            $price = round($price / $rule->price_round) * $rule->price_round;
        }

        if ($rule->price_surcharge) {
            $price += $this->convertRuleAmount($rule, $rule->price_surcharge, $product, $targetUom, $currency, $date, $company);
        }

        if ($rule->price_min_margin) {
            $price = max($price, $priceLimit + $this->convertRuleAmount($rule, $rule->price_min_margin, $product, $targetUom, $currency, $date, $company));
        }

        if ($rule->price_max_margin) {
            $price = min($price, $priceLimit + $this->convertRuleAmount($rule, $rule->price_max_margin, $product, $targetUom, $currency, $date, $company));
        }

        return $price;
    }

    private function basePriceFor(
        PriceRuleBase $base,
        ?PriceList $basePriceList,
        Product $product,
        float $quantity,
        ?UOM $targetUom,
        ?Currency $currency,
        DateTimeInterface $date,
        ?Company $company,
        int $depth,
        array $visited,
    ): float {
        if ($base === PriceRuleBase::PRICE_RULES && $basePriceList) {
            if ($depth >= self::MAX_BASE_DEPTH || in_array($basePriceList->id, $visited, true)) {
                return $this->productPrice($product, PriceRuleBase::LIST_PRICE, $targetUom, $currency, $date, $company);
            }

            $resolved = $this->compute(
                $basePriceList,
                $product,
                $quantity,
                $targetUom,
                $basePriceList->currency ?? $currency,
                $date,
                $company,
                $depth + 1,
                [...$visited, $basePriceList->id],
            );

            return $this->convertCurrency(
                $resolved->price,
                $basePriceList->currency ?? $currency,
                $currency,
                $company,
                $date,
            );
        }

        return $this->productPrice($product, $base, $targetUom, $currency, $date, $company);
    }

    private function productPrice(
        Product $product,
        PriceRuleBase $base,
        ?UOM $targetUom,
        ?Currency $currency,
        DateTimeInterface $date,
        ?Company $company,
    ): float {
        $amount = $base === PriceRuleBase::STANDARD_PRICE
            ? (float) ($product->cost ?? 0)
            : (float) ($product->price ?? 0);

        $amount = $this->toTargetUomPrice($product, $amount, $targetUom);

        return $this->convertCurrency($amount, $this->defaultCurrency(), $currency, $company, $date);
    }

    private function convertRuleAmount(
        PriceRuleItem $rule,
        ?float $amount,
        Product $product,
        ?UOM $targetUom,
        ?Currency $currency,
        DateTimeInterface $date,
        ?Company $company,
    ): float {
        $amount = $this->toTargetUomPrice($product, (float) $amount, $targetUom);

        $from = $rule->currency ?? $rule->priceList?->currency ?? $currency;

        return $this->convertCurrency($amount, $from, $currency, $company, $date);
    }

    private function toTargetUomPrice(Product $product, float $amount, ?UOM $targetUom): float
    {
        $productUom = $product->uom;

        if (! $productUom || ! $targetUom || $productUom->id === $targetUom->id) {
            return $amount;
        }

        return $productUom->computePrice($amount, $targetUom);
    }

    private function toProductQuantity(Product $product, float $quantity, ?UOM $fromUom): float
    {
        $productUom = $product->uom;

        if (! $productUom || ! $fromUom || $fromUom->id === $productUom->id) {
            return $quantity;
        }

        return (float) $fromUom->computeQuantity($quantity, $productUom, false, raiseIfFailure: false);
    }

    private function convertCurrency(
        float $amount,
        ?Currency $from,
        ?Currency $to,
        ?Company $company,
        DateTimeInterface $date,
    ): float {
        if (! $amount || ! $from || ! $to || $from->id === $to->id) {
            return $amount;
        }

        return $from->convert($amount, $to, $company, $date, round: false);
    }

    private function findRule(PriceList $priceList, Product $product, float $quantity, DateTimeInterface $date): ?PriceRuleItem
    {
        $categoryIds = $this->categoryAncestorIds($product);

        return $this->candidateRules($priceList, $product, $categoryIds, $date)
            ->first(fn (PriceRuleItem $rule) => $rule->isApplicableFor($product, $quantity, $categoryIds));
    }

    private function candidateRules(PriceList $priceList, Product $product, array $categoryIds, DateTimeInterface $date): Collection
    {
        $productIds = array_values(array_filter([
            $product->id,
            $product->parent_id,
            ...$product->is_configurable ? $product->variants()->pluck('id')->all() : [],
        ]));

        return PriceRuleItem::query()
            ->where('price_list_id', $priceList->id)
            ->where(fn ($query) => $query->whereNull('starts_at')->orWhere('starts_at', '<=', $date))
            ->where(fn ($query) => $query->whereNull('ends_at')->orWhere('ends_at', '>=', $date))
            ->where(fn ($query) => $query->whereNull('product_id')->orWhereIn('product_id', $productIds))
            ->where(fn ($query) => $query->whereNull('category_id')->orWhereIn('category_id', $categoryIds ?: [0]))
            ->with(['basePriceList', 'currency', 'priceList'])
            ->get()
            ->sort($this->rulePrecedence())
            ->values();
    }

    private function rulePrecedence(): callable
    {
        return function (PriceRuleItem $a, PriceRuleItem $b): int {
            return [$a->apply_to->precedence(), -$a->min_quantity, -$a->category_id, -$a->id]
                <=> [$b->apply_to->precedence(), -$b->min_quantity, -$b->category_id, -$b->id];
        };
    }

    private function categoryAncestorIds(Product $product): array
    {
        if (! $product->category_id) {
            return [];
        }

        $ids = [];

        $category = $product->category;

        $guard = 0;

        while ($category && $guard++ < 50) {
            if (in_array($category->id, $ids, true)) {
                break;
            }

            $ids[] = $category->id;

            $category = $category->parent_id
                ? Category::find($category->parent_id)
                : null;
        }

        return $ids;
    }

    private function defaultCurrency(): ?Currency
    {
        return Currency::find(default_currency_id());
    }
}
