<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Enums\Applicability;
use Webkul\Security\Models\User;

class AccountTagFactory extends Factory
{
    public function definition(): array
    {
        return [
            'color'         => $this->faker->hexColor,
            'country_id'    => null,
            'creator_id'    => User::factory(),
            'applicability' => $this->faker->randomElement(Applicability::options()),
            'name'          => $this->faker->word,
            'tax_negate'    => 0,
        ];
    }
}
