<?php

namespace Webkul\Blog\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Webkul\Blog\Models\Category;
use Webkul\Blog\Models\Post;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(6);

        return [
            'title'            => $title,
            'sub_title'        => $this->faker->sentence(10),
            'content'          => $this->faker->paragraphs(5, true),
            'slug'             => Str::slug($title),
            'image'            => null,
            'author_name'      => $this->faker->name(),
            'is_published'     => false,
            'published_at'     => null,
            'visits'           => 0,
            'meta_title'       => $title,
            'meta_keywords'    => implode(', ', $this->faker->words(5)),
            'meta_description' => $this->faker->sentence(15),
            'category_id'      => Category::factory(),
            'author_id'        => User::factory(),
            'creator_id'       => User::factory(),
            'last_editor_id'   => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ]);
    }
}
