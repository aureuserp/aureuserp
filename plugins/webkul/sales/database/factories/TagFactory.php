<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Sale\Models\Tag;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tag::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'color'      => fake()->hexColor(),
            'name'       => fake()->words(2, true),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }
}
