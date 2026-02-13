<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\PaymentMethod;
use Webkul\Security\Models\User;

/**
 * @extends Factory<\App\Models\PaymentMethod>
 */
class PaymentMethodFactory extends Factory
{
    protected $model = PaymentMethod::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code'         => strtoupper($this->faker->unique()->lexify('???')),
            'payment_type' => PaymentType::RECEIVE,
            'name'         => $this->faker->words(2, true),
            'created_by'   => User::factory(),
        ];
    }

    public function outbound(): static
    {
        return $this->state(fn (array $attributes) => [
            'payment_type' => PaymentType::SEND,
        ]);
    }
}
