<?php

namespace Webkul\Field\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Field\Models\Field;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Field>
 */
class FieldFactory extends Factory
{
    protected $model = Field::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'label' => fake()->words(2, true),
            'type' => 'text',
            'sort' => 1,
            'is_multiselect' => false,
            'is_required' => false,
            'is_unique' => false,
            'is_searchable' => false,
            'is_filterable' => false,
            'is_sortable' => false,
            'is_visible' => true,
            'model_type' => null,
            'options' => null,
            'form_settings' => null,
            'table_settings' => null,
            'infolist_settings' => null,

            // Relationships
            'creator_id' => User::factory(),
        ];
    }

    public function select(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'select',
            'options' => ['Option 1', 'Option 2', 'Option 3'],
        ]);
    }

    public function multiselect(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'select',
            'is_multiselect' => true,
            'options' => ['Option 1', 'Option 2', 'Option 3'],
        ]);
    }

    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }

    public function unique(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_unique' => true,
        ]);
    }

    public function searchable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_searchable' => true,
        ]);
    }

    public function forModel(string $modelType): static
    {
        return $this->state(fn (array $attributes) => [
            'model_type' => $modelType,
        ]);
    }
}
