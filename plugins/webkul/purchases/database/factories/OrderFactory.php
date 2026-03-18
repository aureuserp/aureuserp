<?php

namespace Webkul\Purchase\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Partner\Models\Partner;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Models\Order;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'            => 'PO-'.fake()->unique()->numberBetween(1000, 9999),
            'state'           => OrderState::DRAFT,
            'ordered_at'      => now(),
            'untaxed_amount'  => 0,
            'tax_amount'      => 0,
            'total_amount'    => 0,
            'total_cc_amount' => 0,
            'currency_rate'   => 1.0,
            'partner_id'      => Partner::query()->value('id') ?? Partner::factory(),
            'currency_id'     => Currency::factory(),
            'company_id'      => Company::factory(),
            'creator_id'      => User::query()->value('id') ?? User::factory(),
        ];
    }

    /**
     * Draft state.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::DRAFT,
        ]);
    }

    /**
     * Sent state.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::SENT,
        ]);
    }

    /**
     * Purchase/confirmed state.
     */
    public function purchase(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::PURCHASE,
        ]);
    }

    /**
     * Done/locked state.
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::DONE,
        ]);
    }

    /**
     * Canceled state.
     */
    public function canceled(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::CANCELED,
        ]);
    }
}
