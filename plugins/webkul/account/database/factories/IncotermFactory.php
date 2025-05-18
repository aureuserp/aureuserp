<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;

class IncotermFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'       => $this->faker->word,
            'code'       => $this->faker->word,
            'creator_id' => User::factory(),
        ];
    }
}
