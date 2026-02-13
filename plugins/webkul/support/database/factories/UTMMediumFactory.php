<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UTMMedium;

/**
 * @extends Factory<UTMMedium>
 */
class UTMMediumFactory extends Factory
{
    protected $model = UTMMedium::class;

    public function definition(): array
    {
        return [
            'name'       => $this->faker->words(2, true),
            'creator_id' => User::factory(),
        ];
    }
}
