<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Models\Bill;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Bill>
 */
class BillFactory extends Factory
{
    protected $model = Bill::class;

    public function definition(): array
    {
        $value = fake()->randomElement([0.05, 0.10, 0.50, 1, 2, 5, 10, 20, 50, 100]);

        return [
            'name'               => number_format($value, 2, '.', ''),
            'value'              => $value,
            'sort'               => fake()->numberBetween(1, 20),
            'is_for_all_configs' => true,
            'creator_id'         => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function forAllConfigs(bool $condition = true): static
    {
        return $this->state(fn () => ['is_for_all_configs' => $condition]);
    }
}
