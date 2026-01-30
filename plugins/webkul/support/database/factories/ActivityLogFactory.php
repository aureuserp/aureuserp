<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Support\Models\ActivityLog;

/**
 * @extends Factory<ActivityLog>
 */
class ActivityLogFactory extends Factory
{
    protected $model = ActivityLog::class;

    public function definition(): array
    {
        return [
            'log_name'     => $this->faker->word(),
            'description'  => $this->faker->sentence(),
            'subject_type' => null,
            'subject_id'   => null,
            'event'        => $this->faker->randomElement(['created', 'updated', 'deleted']),
            'causer_type'  => null,
            'causer_id'    => null,
            'properties'   => null,
        ];
    }
}
