<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\BankStatement;
use Webkul\Account\Models\Journal;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<\App\Models\BankStatement>
 */
class BankStatementFactory extends Factory
{
    protected $model = BankStatement::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $balanceStart = fake()->randomFloat(2, 0, 10000);
        $balanceEnd = $balanceStart + fake()->randomFloat(2, -1000, 1000);

        return [
            'company_id'       => Company::factory(),
            'journal_id'       => Journal::factory(),
            'creator_id'       => User::query()->value('id') ?? User::factory(),
            'name'             => fake()->words(2, true),
            'reference'        => fake()->optional()->bothify('STMT-####'),
            'first_line_index' => 0,
            'date'             => fake()->date(),
            'balance_start'    => $balanceStart,
            'balance_end'      => $balanceEnd,
            'balance_end_real' => $balanceEnd,
            'is_completed'     => false,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_completed' => true,
        ]);
    }
}
