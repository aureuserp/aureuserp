<?php

namespace Webkul\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Manufacturing\Enums\OperationTimeMode;
use Webkul\Manufacturing\Enums\OperationWorksheetType;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\Operation;
use Webkul\Manufacturing\Models\WorkCenter;
use Webkul\Security\Models\User;

/**
 * @extends Factory<Operation>
 */
class OperationFactory extends Factory
{
    protected $model = Operation::class;

    public function definition(): array
    {
        return [
            'sort'                       => fake()->numberBetween(1, 100),
            'time_mode_batch'            => 10,
            'name'                       => fake()->words(3, true),
            'worksheet_type'             => OperationWorksheetType::TEXT,
            'worksheet'                  => null,
            'worksheet_google_slide_url' => null,
            'time_mode'                  => OperationTimeMode::MANUAL,
            'note'                       => fake()->sentence(),
            'manual_cycle_time'          => fake()->randomFloat(4, 1, 120),
            'work_center_id'             => WorkCenter::factory(),
            'bill_of_material_id'        => BillOfMaterial::factory(),
            'creator_id'                 => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function manualCycle(float $minutes): static
    {
        return $this->state(fn () => [
            'time_mode'         => OperationTimeMode::MANUAL,
            'manual_cycle_time' => $minutes,
        ]);
    }

    public function auto(): static
    {
        return $this->state(fn () => ['time_mode' => OperationTimeMode::AUTO]);
    }
}
