<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Recruitment\Models\Applicant;
use Webkul\Recruitment\Models\ApplicantApplicantCategory;
use Webkul\Recruitment\Models\ApplicantCategory;

/**
 * @extends Factory<ApplicantApplicantCategory>
 */
class ApplicantApplicantCategoryFactory extends Factory
{
    protected $model = ApplicantApplicantCategory::class;

    public function definition(): array
    {
        return [
            'applicant_id' => Applicant::factory(),
            'applicant_category_id' => ApplicantCategory::factory(),
        ];
    }
}
