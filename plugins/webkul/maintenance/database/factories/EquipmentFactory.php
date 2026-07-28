<?php

namespace Webkul\Maintenance\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Maintenance\Models\EquipmentCategory;
use Webkul\Maintenance\Models\Team;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

/**
 * @extends Factory<Equipment>
 */
class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition(): array
    {
        return [
            'partner_ref'             => strtoupper(fake()->bothify('EQ-####')),
            'location'                => null,
            'model'                   => null,
            'serial_no'               => null,
            'effective_date'          => fake()->date(),
            'warranty_date'           => null,
            'assigned_at'             => null,
            'scraped_at'              => null,
            'name'                    => fake()->words(3, true),
            'note'                    => null,
            'cost'                    => null,
            'maintenance_count'       => 0,
            'maintenance_open_count'  => 0,
            'expected_mtbf'           => null,
            'category_id'             => null,
            'partner_id'              => null,
            'owner_user_id'           => null,
            'maintenance_team_id'     => null,
            'technician_user_id'      => null,
            'company_id'              => Company::factory(),
            'creator_id'              => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function withCategory(): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => EquipmentCategory::factory(),
        ]);
    }

    public function withPartner(): static
    {
        return $this->state(fn (array $attributes) => [
            'partner_id' => Partner::query()->value('id') ?? Partner::factory(),
        ]);
    }

    public function withOwner(): static
    {
        return $this->state(fn (array $attributes) => [
            'owner_user_id' => User::query()->value('id') ?? User::factory(),
        ]);
    }

    public function withTeam(): static
    {
        return $this->state(fn (array $attributes) => [
            'maintenance_team_id' => Team::factory(),
        ]);
    }

    public function withTechnician(): static
    {
        return $this->state(fn (array $attributes) => [
            'technician_user_id' => User::query()->value('id') ?? User::factory(),
        ]);
    }

    public function withWarranty(): static
    {
        return $this->state(fn (array $attributes) => [
            'warranty_date' => now()->addYear(),
        ]);
    }

    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'assigned_at' => now(),
        ]);
    }

    public function scraped(): static
    {
        return $this->state(fn (array $attributes) => [
            'scraped_at' => now(),
        ]);
    }

    public function withCost(): static
    {
        return $this->state(fn (array $attributes) => [
            'cost' => fake()->randomFloat(2, 100, 10000),
        ]);
    }
}
