<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Recruitment\Models\JobPosition;
use Webkul\Recruitment\Models\Stage;
use Webkul\Recruitment\Models\StageJob;

/**
 * @extends Factory<StageJob>
 */
class StageJobFactory extends Factory
{
    protected $model = StageJob::class;

    public function definition(): array
    {
        return [
            'stage_id' => Stage::factory(),
            'job_id' => JobPosition::factory(),
        ];
    }
}
