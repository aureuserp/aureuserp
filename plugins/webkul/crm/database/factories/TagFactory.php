<?php

namespace Webkul\Crm\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Crm\Models\Tag;
use Webkul\Security\Models\User;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        return [
            'name'       => fake()->unique()->words(2, true),
            'color'      => fake()->hexColor(),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }
}
