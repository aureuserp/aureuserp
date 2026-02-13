<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\AccountPaymentRegisterMoveLine;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\PaymentRegister;

/**
 * @extends Factory<\App\Models\AccountPaymentRegisterMoveLine>
 */
class AccountPaymentRegisterMoveLineFactory extends Factory
{
    protected $model = AccountPaymentRegisterMoveLine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payment_register_id' => PaymentRegister::factory(),
            'move_line_id'        => MoveLine::factory(),
        ];
    }
}
