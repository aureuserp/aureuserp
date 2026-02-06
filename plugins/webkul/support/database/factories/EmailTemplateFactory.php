<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Support\Models\EmailTemplate;

/**
 * @extends Factory<EmailTemplate>
 */
class EmailTemplateFactory extends Factory
{
    protected $model = EmailTemplate::class;

    public function definition(): array
    {
        return [
            'code'        => $this->faker->unique()->slug(),
            'name'        => $this->faker->words(3, true),
            'subject'     => $this->faker->sentence(),
            'content'     => $this->faker->paragraphs(3, true),
            'description' => $this->faker->optional()->sentence(),
            'is_active'   => true,
            'sender_name' => $this->faker->optional()->name(),
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
