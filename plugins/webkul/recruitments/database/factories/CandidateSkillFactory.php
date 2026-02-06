<?php

namespace Webkul\Recruitment\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Employee\Models\Skill;
use Webkul\Employee\Models\SkillLevel;
use Webkul\Employee\Models\SkillType;
use Webkul\Recruitment\Models\Candidate;
use Webkul\Recruitment\Models\CandidateSkill;
use Webkul\Security\Models\User;

/**
 * @extends Factory<CandidateSkill>
 */
class CandidateSkillFactory extends Factory
{
    protected $model = CandidateSkill::class;

    public function definition(): array
    {
        return [
            'skill_id' => Skill::factory(),
            'skill_level_id' => SkillLevel::factory(),
            'skill_type_id' => SkillType::factory(),
            'user_id' => null,
            'creator_id' => User::factory(),
            'candidate_id' => Candidate::factory(),
        ];
    }

    public function withUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::factory(),
        ]);
    }
}
