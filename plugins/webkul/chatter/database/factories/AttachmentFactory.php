<?php

namespace Webkul\Chatter\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Chatter\Models\Attachment;
use Webkul\Chatter\Models\Message;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Attachment>
 */
class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word().'.pdf',
            'original_file_name' => fake()->word().'.pdf',
            'file_path' => 'attachments/'.fake()->uuid().'.pdf',
            'file_size' => fake()->numberBetween(1000, 5000000),
            'mime_type' => 'application/pdf',
            'messageable' => null,

            // Relationships
            'message_id' => Message::factory(),
            'company_id' => Company::factory(),
            'creator_id' => User::factory(),
        ];
    }

    public function image(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => fake()->word().'.jpg',
            'original_file_name' => fake()->word().'.jpg',
            'file_path' => 'attachments/'.fake()->uuid().'.jpg',
            'mime_type' => 'image/jpeg',
        ]);
    }

    public function withMessageable(): static
    {
        return $this->state(fn (array $attributes) => [
            'messageable' => fake()->sentence(),
        ]);
    }
}
