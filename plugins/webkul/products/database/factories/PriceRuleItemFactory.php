<?php

namespace Webkul\Product\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Product\Enums\PriceRuleApplyTo;
use Webkul\Product\Enums\PriceRuleBase;
use Webkul\Product\Enums\PriceRuleType;
use Webkul\Product\Models\Category;
use Webkul\Product\Models\PriceList;
use Webkul\Product\Models\PriceRuleItem;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<PriceRuleItem>
 */
class PriceRuleItemFactory extends Factory
{
    use HasCompanyDefault;

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PriceRuleItem::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'price_list_id'    => PriceList::factory(),
            'apply_to'         => PriceRuleApplyTo::GLOBAL,
            'base'             => PriceRuleBase::LIST_PRICE,
            'type'             => PriceRuleType::FIXED,
            'min_quantity'     => 0,
            'fixed_price'      => fake()->randomFloat(2, 1, 100),
            'creator_id'       => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function forPriceList(PriceList|int $priceList): static
    {
        return $this->state([
            'price_list_id' => $priceList instanceof PriceList ? $priceList->id : $priceList,
        ]);
    }

    public function fixed(float $price): static
    {
        return $this->state([
            'type'        => PriceRuleType::FIXED,
            'fixed_price' => $price,
        ]);
    }

    public function percentage(float $percent): static
    {
        return $this->state([
            'type'          => PriceRuleType::PERCENTAGE,
            'percent_price' => $percent,
        ]);
    }

    public function formula(array $attributes = []): static
    {
        return $this->state(array_merge([
            'type' => PriceRuleType::FORMULA,
        ], $attributes));
    }

    public function forProduct(Product|int $product): static
    {
        return $this->state([
            'apply_to'   => PriceRuleApplyTo::PRODUCT,
            'product_id' => $product instanceof Product ? $product->id : $product,
        ]);
    }

    public function forVariant(Product|int $product): static
    {
        return $this->state([
            'apply_to'   => PriceRuleApplyTo::VARIANT,
            'product_id' => $product instanceof Product ? $product->id : $product,
        ]);
    }

    public function forCategory(Category|int $category): static
    {
        return $this->state([
            'apply_to'    => PriceRuleApplyTo::CATEGORY,
            'category_id' => $category instanceof Category ? $category->id : $category,
        ]);
    }
}
