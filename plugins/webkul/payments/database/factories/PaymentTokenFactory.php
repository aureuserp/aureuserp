<?php

namespace Webkul\Payment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\PaymentMethod;
use Webkul\Partner\Models\Partner;
use Webkul\Payment\Models\PaymentToken;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<\App\Models\PaymentToken>
 */
class PaymentTokenFactory extends Factory
{
    protected $model = PaymentToken::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id'        => Company::factory(),
            'payment_method_id' => PaymentMethod::factory(),
            'partner_id'        => Partner::query()->value('id') ?? Partner::factory(),
            'creator_id'        => User::query()->value('id') ?? User::factory(),
            'payment_details'   => [
                'token' => fake()->uuid(),
                'type'  => 'card',
                'last4' => fake()->numerify('####'),
            ],
            'provider_reference_id' => fake()->uuid(),
            'is_active'             => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
