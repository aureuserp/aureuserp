<?php

namespace Webkul\Blog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Webkul\Blog\Models\Category;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
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
        $name = fake()->words(2, true);

        return [
            'name'             => $name,
            'sub_title'        => fake()->sentence(8),
            'slug'             => Str::slug($name),
            'image'            => null,
            'meta_title'       => $name,
            'meta_keywords'    => implode(', ', fake()->words(5)),
            'meta_description' => fake()->sentence(12),
            'creator_id'       => User::query()->value('id') ?? User::factory(),
        ];
    }
}
