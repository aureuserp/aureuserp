<?php

namespace Webkul\Chatter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Chatter\Models\Message;
use Webkul\Support\Models\ActivityType;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'type'             => 'comment',
            'name'             => fake()->sentence(),
            'subject'          => fake()->sentence(),
            'body'             => fake()->paragraph(),
            'summary'          => null,
            'messageable_id'   => null,
            'messageable_type' => null,

            // Relationships
            'company_id'       => Company::factory(),
            'activity_type_id' => null,
        ];
    }

    public function email(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'email',
        ]);
    }

    public function notification(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'notification',
        ]);
    }

    public function withActivityType(): static
    {
        return $this->state(fn (array $attributes) => [
            'activity_type_id' => ActivityType::factory(),
        ]);
    }

    public function withSummary(): static
    {
        return $this->state(fn (array $attributes) => [
            'summary' => fake()->sentence(),
        ]);
    }

    public function messageable($messageable): static
    {
        return $this->state(fn (array $attributes) => [
            'messageable_id'   => $messageable->id,
            'messageable_type' => get_class($messageable),
        ]);
    }
}
