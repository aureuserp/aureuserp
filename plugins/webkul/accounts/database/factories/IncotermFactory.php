<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\Incoterm;
use Webkul\Security\Models\User;

class IncotermFactory extends Factory
{
    protected $model = Incoterm::class;

    public function definition(): array
    {
        return [
            'code'       => strtoupper($this->faker->unique()->lexify('???')),
            'name'       => $this->faker->words(3, true),
            'creator_id' => User::factory(),
        ];
    }
}
