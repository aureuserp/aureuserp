<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class PaymentTermFactory extends Factory
{
    protected $model = PaymentTerm::class;

    public function definition(): array
    {
        return [
            'company_id'          => Company::factory(),
            'sort'                => $this->faker->randomNumber(),
            'discount_days'       => $this->faker->randomElement([0, 10, 15, 30]),
            'creator_id'          => User::factory(),
            'early_pay_discount'  => $this->faker->boolean(),
            'name'                => $this->faker->sentence(3),
            'note'                => $this->faker->optional()->text(200),
            'display_on_invoice'  => $this->faker->boolean(),
            'early_discount'      => $this->faker->boolean(),
            'discount_percentage' => $this->faker->randomFloat(2, 0, 20),
            'created_at'          => now(),
            'updated_at'          => now(),
        ];
    }
}
