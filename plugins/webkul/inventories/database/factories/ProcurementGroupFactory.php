<?php

namespace Webkul\Inventory\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\ProcurementGroup;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;

/**
 * @extends Factory<ProcurementGroup>
 */
class ProcurementGroupFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ProcurementGroup::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'       => fake()->words(2, true),
            'move_type'  => 'direct',
            'partner_id' => Partner::query()->value('id') ?? Partner::factory(),
            'creator_id' => User::query()->value('id') ?? User::factory(),
        ];
    }
}
