<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Recruitment\Models\ApplicantCategory;
use Webkul\Recruitment\Models\Candidate;
use Webkul\Recruitment\Models\CandidateApplicantCategory;

/**
 * @extends Factory<CandidateApplicantCategory>
 */
class CandidateApplicantCategoryFactory extends Factory
{
    protected $model = CandidateApplicantCategory::class;

    public function definition(): array
    {
        return [
            'candidate_id' => Candidate::factory(),
            'applicant_category_id' => ApplicantCategory::factory(),
        ];
    }
}
