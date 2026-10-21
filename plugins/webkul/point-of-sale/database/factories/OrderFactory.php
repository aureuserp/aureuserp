<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Enums\OrderState;
use Webkul\PointOfSale\Models\Order;
use Webkul\PointOfSale\Models\Session;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'state'      => OrderState::DRAFT,
            'session_id' => Session::query()->value('id') ?? Session::factory()->opened(),
            'user_id'    => User::query()->value('id') ?? User::factory(),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'state'        => OrderState::PAID,
            'confirmed_at' => now(),
        ]);
    }

    public function done(): static
    {
        return $this->state(fn () => [
            'state'        => OrderState::DONE,
            'confirmed_at' => now(),
        ]);
    }

    public function canceled(): static
    {
        return $this->state(fn () => ['state' => OrderState::CANCELED]);
    }

    public function toInvoice(): static
    {
        return $this->state(fn () => ['is_to_invoice' => true]);
    }

    public function refundOf(Order|int $order): static
    {
        return $this->state(fn () => [
            'refunded_order_id' => $order instanceof Order ? $order->id : $order,
        ]);
    }
}
