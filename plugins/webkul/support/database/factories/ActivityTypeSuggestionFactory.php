<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Support\Models\ActivityType;
use Webkul\Support\Models\ActivityTypeSuggestion;

/**
 * @extends Factory<ActivityTypeSuggestion>
 */
class ActivityTypeSuggestionFactory extends Factory
{
    protected $model = ActivityTypeSuggestion::class;

    public function definition(): array
    {
        return [
            'activity_type_id'           => ActivityType::factory(),
            'suggested_activity_type_id' => ActivityType::factory(),
        ];
    }
}
