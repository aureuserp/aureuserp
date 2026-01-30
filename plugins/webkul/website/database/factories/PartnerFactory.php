<?php

namespace Webkul\Website\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Webkul\Partner\Database\Factories\PartnerFactory as BasePartnerFactory;
use Webkul\Website\Models\Partner;

/**
 * @extends Factory<Partner>
 */
class PartnerFactory extends BasePartnerFactory
{
    protected $model = Partner::class;

    public function definition(): array
    {
        return array_merge(parent::definition(), [
            'password'          => null,
            'is_active'         => true,
            'email_verified_at' => null,
        ]);
    }

    public function withPassword(string $password = 'password'): static
    {
        return $this->state(fn (array $attributes) => [
            'password' => Hash::make($password),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => now(),
        ]);
    }
}
