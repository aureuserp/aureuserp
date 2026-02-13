<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\UTMSource;

/**
 * @extends Factory<UTMSource>
 */
class UTMSourceFactory extends Factory
{
    protected $model = UTMSource::class;

    public function definition(): array
    {
        return [
            'name'       => $this->faker->words(2, true),
            'creator_id' => User::factory(),
        ];
    }
}
