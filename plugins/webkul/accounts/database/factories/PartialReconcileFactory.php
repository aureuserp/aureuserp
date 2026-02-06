<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\FullReconcile;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\PartialReconcile;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * @extends Factory<\App\Models\PartialReconcile>
 */
class PartialReconcileFactory extends Factory
{
    protected $model = PartialReconcile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = $this->faker->randomFloat(2, 10, 1000);

        return [
            'debit_move_id'          => MoveLine::factory(),
            'credit_move_id'         => MoveLine::factory(),
            'full_reconcile_id'      => null,
            'exchange_move_id'       => null,
            'debit_currency_id'      => Currency::factory(),
            'credit_currency_id'     => Currency::factory(),
            'company_id'             => Company::factory(),
            'created_by'             => User::factory(),
            'max_date'               => $this->faker->date(),
            'amount'                 => $amount,
            'debit_amount_currency'  => $amount,
            'credit_amount_currency' => $amount,
        ];
    }

    public function fullReconciled(): static
    {
        return $this->state(fn (array $attributes) => [
            'full_reconcile_id' => FullReconcile::factory(),
        ]);
    }
}
