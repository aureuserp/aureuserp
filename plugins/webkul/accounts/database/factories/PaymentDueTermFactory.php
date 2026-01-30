<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\DelayType;
use Webkul\Account\Enums\DueTermValue;
use Webkul\Account\Models\PaymentDueTerm;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Security\Models\User;

class PaymentDueTermFactory extends Factory
{
    protected $model = PaymentDueTerm::class;

    public function definition(): array
    {
        return [
            'payment_id'      => PaymentTerm::factory(),
            'creator_id'      => User::factory(),
            'value'           => DueTermValue::PERCENT,
            'value_amount'    => 100.0,
            'delay_type'      => DelayType::DAYS_AFTER,
            'days_next_month' => 0,
            'nb_days'         => 30,
        ];
    }

    public function fixed(): static
    {
        return $this->state(fn (array $attributes) => [
            'value'        => DueTermValue::FIXED,
            'value_amount' => $this->faker->randomFloat(2, 100, 1000),
        ]);
    }

    public function endOfMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'delay_type' => DelayType::DAYS_AFTER_END_OF_MONTH,
        ]);
    }
}
