<?php

namespace Webkul\TableView\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\TableViews\Models\TableViewFavorite;

/**
 * @extends Factory<TableViewFavorite>
 */
class TableViewFavoriteFactory extends Factory
{
    protected $model = TableViewFavorite::class;

    public function definition(): array
    {
        return [
            'is_favorite'     => true,
            'view_type'       => 'table',
            'view_key'        => fake()->word(),
            'filterable_type' => null,

            // Relationships
            'user_id' => User::factory(),
        ];
    }

    public function notFavorite(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_favorite' => false,
        ]);
    }

    public function kanban(): static
    {
        return $this->state(fn (array $attributes) => [
            'view_type' => 'kanban',
        ]);
    }

    public function forModel(string $modelType): static
    {
        return $this->state(fn (array $attributes) => [
            'filterable_type' => $modelType,
        ]);
    }
}
