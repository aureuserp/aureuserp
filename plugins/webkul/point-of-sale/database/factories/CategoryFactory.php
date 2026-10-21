<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Models\Category;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->unique()->words(2, true),
            'sort'       => fake()->numberBetween(1, 50),
            'color'      => (string) fake()->numberBetween(1, 9),
            'parent_id'  => null,
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function childOf(Category|int $parent): static
    {
        return $this->state(fn () => [
            'parent_id' => $parent instanceof Category ? $parent->id : $parent,
        ]);
    }
}
