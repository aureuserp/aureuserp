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
            'sort'                => fake()->randomNumber(),
            'discount_days'       => fake()->randomElement([0, 10, 15, 30]),
            'creator_id'          => User::query()->value('id') ?? User::factory(),
            'early_pay_discount'  => fake()->boolean(),
            'name'                => fake()->sentence(3),
            'note'                => fake()->optional()->text(200),
            'display_on_invoice'  => fake()->boolean(),
            'early_discount'      => fake()->boolean(),
            'discount_percentage' => fake()->randomFloat(2, 0, 20),
            'created_at'          => now(),
            'updated_at'          => now(),
        ];
    }
}
