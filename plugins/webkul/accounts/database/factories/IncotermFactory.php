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
            'code'       => strtoupper(fake()->unique()->lexify('???')),
            'name'       => fake()->words(3, true),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }
}
