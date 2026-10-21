<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Payment;
use Webkul\PointOfSale\Models\PaymentMethod;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Payment::class;

    public function definition(): array
    {
        $order = Order::query()->value('id') ?? Order::factory();

        return [
            'amount'            => fake()->randomFloat(2, 1, 500),
            'is_change'         => false,
            'order_id'          => $order,
            'session_id'        => fn (array $attributes) => Order::withoutGlobalScopes()
                ->whereKey($attributes['order_id'])
                ->value('session_id'),
            'payment_method_id' => PaymentMethod::query()->value('id') ?? PaymentMethod::factory(),
            'user_id'           => User::query()->value('id') ?? User::factory(),
            'creator_id'        => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function amount(float $amount): static
    {
        return $this->state(fn () => ['amount' => $amount]);
    }

    public function change(float $amount): static
    {
        return $this->state(fn () => [
            'amount'    => -1 * abs($amount),
            'is_change' => true,
        ]);
    }
}
