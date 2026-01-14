<?php

namespace Webkul\Blog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Blog\Models\Tag;
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
            'name'       => fake()->name(),
            'color'      => fake()->hexColor(),
            'creator_id' => User::factory(),
        ];
    }
}
