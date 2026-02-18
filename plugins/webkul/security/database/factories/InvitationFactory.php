<?php

namespace Webkul\Security\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\Invitation;
use Webkul\Security\Models\Role;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Invitation>
 */
class InvitationFactory extends Factory
{
    protected $model = Invitation::class;

    public function definition(): array
    {
        return [
            'email' => fake()->safeEmail(),
            'role_id' => Role::factory(),
            'token' => fake()->uuid(),
            'expires_at' => now()->addDays(7),
            'invited_by' => User::factory(),
        ];
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }
}
