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
            'value'           => $this->faker->randomElement([DueTermValue::PERCENT->value, DueTermValue::FIXED->value]),
            'value_amount'    => $this->faker->randomFloat(2, 0, 100),
            'delay_type'      => DelayType::DAYS_AFTER->value,
            'days_next_month' => $this->faker->numberBetween(0, 31),
            'nb_days'         => $this->faker->numberBetween(0, 60),
            'created_at'      => now(),
            'updated_at'      => now(),
        ];
    }
}
