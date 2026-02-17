<?php

namespace Webkul\Accounting\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Database\Factories\CategoryFactory as AccountCategoryFactory;
use Webkul\Accounting\Models\Category;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends AccountCategoryFactory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'product_properties_definition' => null,
        ]);
    }

    public function withProductProperties(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_properties_definition' => fake()->sentence(),
        ]);
    }
}
