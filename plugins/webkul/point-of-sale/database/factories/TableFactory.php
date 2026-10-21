<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Enums\TableShape;
use Webkul\PointOfSale\Models\Floor;
use Webkul\PointOfSale\Models\Table;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Table>
 */
class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition(): array
    {
        return [
            'table_number' => (string) fake()->unique()->numberBetween(1, 200),
            'shape'        => TableShape::SQUARE,
            'position_h'   => fake()->randomFloat(4, 0, 500),
            'position_v'   => fake()->randomFloat(4, 0, 500),
            'width'        => 50,
            'height'       => 50,
            'seats'        => fake()->numberBetween(2, 8),
            'floor_id'     => Floor::query()->value('id') ?? Floor::factory(),
            'creator_id'   => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function round(): static
    {
        return $this->state(fn () => ['shape' => TableShape::ROUND]);
    }

    public function seats(int $seats): static
    {
        return $this->state(fn () => ['seats' => $seats]);
    }
}
