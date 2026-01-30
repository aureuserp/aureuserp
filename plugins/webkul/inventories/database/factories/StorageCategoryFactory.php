<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Security\Models\User;

/**
 * @extends Factory<StorageCategory>
 */
class StorageCategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = StorageCategory::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'               => fake()->word(),
            'sort'               => 0,
            'allow_new_products' => \Webkul\Inventory\Enums\AllowNewProduct::MIXED,
            'parent_path'        => null,
            'max_weight'         => null,

            // Relationships
            'company_id' => \Webkul\Support\Models\Company::factory(),
            'creator_id' => User::factory(),
        ];
    }

    public function emptyOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_new_products' => \Webkul\Inventory\Enums\AllowNewProduct::EMPTY,
        ]);
    }

    public function sameProduct(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_new_products' => \Webkul\Inventory\Enums\AllowNewProduct::SAME,
        ]);
    }

    public function withMaxWeight(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_weight' => fake()->randomFloat(2, 10, 1000),
        ]);
    }
}
