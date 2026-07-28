<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Enums\AllowNewProduct;
use Webkul\Inventory\Models\StorageCategory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

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
            'name'               => fake()->words(2, true),
            'sort'               => 0,
            'allow_new_products' => AllowNewProduct::MIXED,
            'max_weight'         => 0.0,

            // Relationships
            'company_id' => Company::factory(),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function emptyOnly(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_new_products' => AllowNewProduct::EMPTY,
        ]);
    }

    public function sameProduct(): static
    {
        return $this->state(fn (array $attributes) => [
            'allow_new_products' => AllowNewProduct::SAME,
        ]);
    }

    public function withMaxWeight(): static
    {
        return $this->state(fn (array $attributes) => [
            'max_weight' => fake()->randomFloat(2, 10, 1000),
        ]);
    }
}
