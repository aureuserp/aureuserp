<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PointOfSale\Models\Note;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Note>
 */
class NoteFactory extends Factory
{
    protected $model = Note::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->words(2, true),
            'color'      => (string) fake()->numberBetween(1, 9),
            'sort'       => fake()->numberBetween(1, 20),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }
}
