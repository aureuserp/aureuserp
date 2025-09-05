<?php

namespace Webkul\Partner\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Partner\Models\BankAccount;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Bank;

/**
 * @extends Factory<BankAccount>
 */
class BankAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BankAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'account_number'      => fake()->bankAccountNumber(),
            'account_holder_name' => fake()->name(),
            'is_active'           => true,
            'can_send_money'      => true,
            'creator_id'          => User::factory(),
            'partner_id'          => Partner::factory(),
            'bank_id'             => Bank::factory(),
        ];
    }
}
