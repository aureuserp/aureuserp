<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Support\Models\EmailLog;

/**
 * @extends Factory<EmailLog>
 */
class EmailLogFactory extends Factory
{
    protected $model = EmailLog::class;

    public function definition(): array
    {
        return [
            'recipient_email' => $this->faker->safeEmail(),
            'recipient_name'  => $this->faker->name(),
            'subject'         => $this->faker->sentence(),
            'status'          => $this->faker->randomElement(['sent', 'failed', 'pending']),
            'error_message'   => null,
            'sent_at'         => $this->faker->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'        => 'failed',
            'error_message' => $this->faker->sentence(),
        ]);
    }
}
