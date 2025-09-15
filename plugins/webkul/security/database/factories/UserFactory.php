<?php

namespace Webkul\Security\Database\Factories;

use Illuminate\Support\Str;
use Webkul\Security\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;
use BezhanSalleh\FilamentShield\Support\Utils as ShieldUtils;


class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'), // password
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function asAdmin(): static
    {
        return $this->afterCreating(function (User $user) {
            $user->assignRole(ShieldUtils::getPanelUserRoleName());
        });
    }
}