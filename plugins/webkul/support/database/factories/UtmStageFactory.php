<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UtmStage;

class UtmStageFactory extends Factory
{
    protected $model = UtmStage::class;

    public function definition(): array
    {
        return [
            'sort'       => 0,
            'name'       => fake()->words(2, true),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }
}
