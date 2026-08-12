<?php

namespace Webkul\Payment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Payment\Models\Payment;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

class PaymentFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'creator_id' => User::query()->value('id') ?? User::factory(),
            'name'       => fake()->words(3, true),
            'amount'     => fake()->randomFloat(2, 10, 1000),
            'date'       => fake()->date(),
        ];
    }
}
