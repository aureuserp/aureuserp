<?php

namespace Webkul\Maintenance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Maintenance\Models\EquipmentCategory;
use Webkul\Maintenance\Models\MaintenanceRequest;
use Webkul\Maintenance\Models\Stage;
use Webkul\Maintenance\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<MaintenanceRequest>
 */
class MaintenanceRequestFactory extends Factory
{
    protected $model = MaintenanceRequest::class;

    public function definition(): array
    {
        return [
            'repeat_interval'          => null,
            'name'                     => fake()->sentence(3),
            'priority'                 => (string) fake()->numberBetween(0, 3),
            'maintenance_type'         => null,
            'instruction_type'         => null,
            'instruction_google_slide' => null,
            'repeat_unit'              => null,
            'repeat_type'              => null,
            'requested_at'             => null,
            'closed_at'                => null,
            'repeat_until'             => null,
            'duration'                 => null,
            'description'              => null,
            'instruction_text'         => null,
            'recurring_maintenance'    => false,
            'scheduled_at'             => null,
            'equipment_id'             => null,
            'stage_id'                 => null,
            'category_id'              => null,
            'user_id'                  => null,
            'maintenance_team_id'      => Team::factory(),
            'company_id'               => Company::factory(),
            'creator_id'               => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function corrective(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_type' => 'corrective',
        ]);
    }

    public function preventive(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_type' => 'preventive',
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'scheduled_at' => now()->addWeek(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'closed_at' => now(),
        ]);
    }

    public function recurring(): static
    {
        return $this->state(fn (array $attributes) => [
            'recurring_maintenance' => true,
            'repeat_interval'       => 1,
            'repeat_unit'           => 'month',
            'repeat_type'           => 'forever',
        ]);
    }

    public function withEquipment(): static
    {
        return $this->state(fn (array $attributes) => [
            'equipment_id' => Equipment::factory(),
        ]);
    }

    public function withStage(): static
    {
        return $this->state(fn (array $attributes) => [
            'stage_id' => Stage::factory(),
        ]);
    }

    public function withCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => EquipmentCategory::factory(),
        ]);
    }

    public function withUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => User::query()->value('id') ?? User::factory(),
        ]);
    }

    public function withDescription(): static
    {
        return $this->state(fn (array $attributes) => [
            'description' => fake()->paragraph(),
        ]);
    }

    public function withTextInstruction(): static
    {
        return $this->state(fn (array $attributes) => [
            'instruction_type' => 'text',
            'instruction_text' => fake()->paragraph(),
        ]);
    }

    public function withGoogleSlideInstruction(): static
    {
        return $this->state(fn (array $attributes) => [
            'instruction_type'         => 'google_slide',
            'instruction_google_slide' => fake()->url(),
        ]);
    }
}
