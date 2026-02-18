<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\PaymentMethod;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\Security\Models\User;

/**
 * @extends Factory<\App\Models\PaymentMethodLine>
 */
class PaymentMethodLineFactory extends Factory
{
    protected $model = PaymentMethodLine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sort'               => 0,
            'payment_method_id'  => PaymentMethod::factory(),
            'payment_account_id' => Account::factory(),
            'journal_id'         => Journal::factory(),
            'name'               => $this->faker->words(2, true),
            'creator_id'         => User::factory(),
        ];
    }
}
