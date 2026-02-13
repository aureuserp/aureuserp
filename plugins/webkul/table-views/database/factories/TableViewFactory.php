<?php

namespace Webkul\TableView\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\TableViews\Models\TableView;

/**
 * @extends Factory<TableView>
 */
class TableViewFactory extends Factory
{
    protected $model = TableView::class;

    public function definition(): array
    {
        return [
            'name'            => fake()->words(2, true),
            'icon'            => null,
            'color'           => null,
            'is_public'       => false,
            'filters'         => null,
            'filterable_type' => null,

            // Relationships
            'user_id' => User::factory(),
        ];
    }

    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    public function withIcon(): static
    {
        return $this->state(fn (array $attributes) => [
            'icon'  => fake()->word(),
            'color' => fake()->hexColor(),
        ]);
    }

    public function withFilters(array $filters = []): static
    {
        return $this->state(fn (array $attributes) => [
            'filters' => $filters ?: ['status' => 'active'],
        ]);
    }

    public function forModel(string $modelType): static
    {
        return $this->state(fn (array $attributes) => [
            'filterable_type' => $modelType,
        ]);
    }
}
