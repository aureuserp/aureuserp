<?php

namespace Webkul\Maintenance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Maintenance\Models\EquipmentCategory;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<EquipmentCategory>
 */
class EquipmentCategoryFactory extends Factory
{
    protected $model = EquipmentCategory::class;

    public function definition(): array
    {
        return [
            'name'               => fake()->words(2, true),
            'note'               => null,
            'creator_id'         => User::query()->value('id') ?? User::factory(),
            'technician_user_id' => null,
            'company_id'         => Company::factory(),
        ];
    }

    public function withNote(): static
    {
        return $this->state(fn (array $attributes) => [
            'note' => fake()->sentence(),
        ]);
    }

    public function withTechnician(): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_user_id' => User::query()->value('id') ?? User::factory(),
        ]);
    }
}
