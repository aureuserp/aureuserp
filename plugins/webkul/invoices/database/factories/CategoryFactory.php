<?php

namespace Webkul\Invoice\Database\Factories;

use Webkul\Account\Database\Factories\CategoryFactory as AccountCategoryFactory;
use Webkul\Invoice\Models\Category;

/**
 * @extends AccountCategoryFactory
 */
class CategoryFactory extends AccountCategoryFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'product_properties_definition' => null,
        ]);
    }

    /**
     * Indicate that the category has product properties definition.
     */
    public function withProductProperties(?array $properties = null): static
    {
        return $this->state(fn (array $attributes) => [
            'product_properties_definition' => $properties ?? [
                'property_1' => 'value_1',
                'property_2' => 'value_2',
            ],
        ]);
    }
}
