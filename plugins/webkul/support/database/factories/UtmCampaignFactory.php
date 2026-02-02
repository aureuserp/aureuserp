<?php

namespace Webkul\Support\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UtmCampaign;
use Webkul\Support\Models\UtmStage;

class UtmCampaignFactory extends Factory
{
    protected $model = UtmCampaign::class;

    public function definition(): array
    {
        return [
            'user_id'          => User::factory(),
            'stage_id'         => UtmStage::factory(),
            'color'            => $this->faker->numberBetween(1, 10),
            'created_by'       => User::factory(),
            'name'             => $this->faker->words(2, true),
            'title'            => $this->faker->sentence(4),
            'is_active'        => true,
            'is_auto_campaign' => false,
            'company_id'       => Company::factory(),
        ];
    }

    public function autoCampaign(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_auto_campaign' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
