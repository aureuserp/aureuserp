<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\Applicability;
use Webkul\Account\Models\Tag;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Country;

class AccountTagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'color'         => $this->faker->hexColor,
            'country_id'    => null,
            'creator_id'    => User::factory(),
            'applicability' => Applicability::TAXES,
            'name'          => $this->faker->word,
            'tax_negate'    => false,
        ];
    }

    public function withCountry(): static
    {
        return $this->state(fn (array $attributes) => [
            'country_id' => Country::factory(),
        ]);
    }

    public function negated(): static
    {
        return $this->state(fn (array $attributes) => [
            'tax_negate' => true,
        ]);
    }
}
