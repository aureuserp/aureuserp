<?php

namespace Webkul\Website\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Webkul\Security\Models\User;
use Webkul\Website\Models\Page;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Page::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence(4);

        return [
            'title'             => $title,
            'content'           => fake()->paragraphs(5, true),
            'slug'              => Str::slug($title),
            'is_published'      => false,
            'published_at'      => null,
            'is_header_visible' => true,
            'is_footer_visible' => true,
            'meta_title'        => $title,
            'meta_keywords'     => implode(', ', fake()->words(5)),
            'meta_description'  => fake()->sentence(15),
            'creator_id'        => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_published' => true,
            'published_at' => fake()->dateTimeBetween('-1 year', 'now'),
        ]);
    }

    public function hideHeaderFooter(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_header_visible' => false,
            'is_footer_visible' => false,
        ]);
    }
}
