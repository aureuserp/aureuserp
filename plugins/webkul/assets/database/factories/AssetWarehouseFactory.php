<?php

namespace Webkul\Asset\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<AssetWarehouse>
 */
class AssetWarehouseFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = AssetWarehouse::class;

    public function definition(): array
    {
        return [
            'name'                  => fake()->company().' Warehouse',
            'code'                  => fake()->unique()->bothify('AWH-####'),
            'custodian_employee_id' => null,
            'is_active'             => true,
            'note'                  => null,
            'creator_id'            => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
