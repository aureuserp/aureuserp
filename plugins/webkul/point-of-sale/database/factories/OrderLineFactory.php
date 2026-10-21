<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Enums\PriceType;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\OrderLine;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<OrderLine>
 */
class OrderLineFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = OrderLine::class;

    public function definition(): array
    {
        return [
            'qty'        => fake()->numberBetween(1, 5),
            'price_unit' => fake()->randomFloat(2, 1, 250),
            'price_type' => PriceType::ORIGINAL,
            'discount'   => 0,
            'order_id'   => Order::query()->value('id') ?? Order::factory(),
            'product_id' => Product::query()->value('id') ?? Product::factory(),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function quantity(float $qty): static
    {
        return $this->state(fn () => ['qty' => $qty]);
    }

    public function price(float $priceUnit): static
    {
        return $this->state(fn () => ['price_unit' => $priceUnit]);
    }

    public function discounted(float $percentage): static
    {
        return $this->state(fn () => ['discount' => $percentage]);
    }

    public function refundOf(OrderLine|int $line): static
    {
        return $this->state(fn () => [
            'refunded_order_line_id' => $line instanceof OrderLine ? $line->id : $line,
        ]);
    }
}
