<?php

namespace Webkul\Asset\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetSourceType;
use Webkul\Asset\Enums\MovementType;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetMovement;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<AssetMovement>
 */
class AssetMovementFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = AssetMovement::class;

    public function definition(): array
    {
        return [
            'asset_id'             => Asset::factory(),
            'type'                 => MovementType::Registered,
            'source_type'          => AssetSourceType::Manual,
            'reference_type'       => null,
            'reference_id'         => null,
            'from_warehouse_id'    => null,
            'from_employee_id'     => null,
            'from_site_id'         => null,
            'to_warehouse_id'      => null,
            'to_employee_id'       => null,
            'to_site_id'           => null,
            'condition'            => AssetCondition::New,
            'occurred_at'          => now(),
            'expected_return_at'   => null,
            'performed_by_user_id' => User::query()->value('id') ?? User::factory(),
            'note'                 => null,
            'attachments'          => null,
        ];
    }

    public function issue(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MovementType::Issued,
        ]);
    }

    public function transfer(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MovementType::Transferred,
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MovementType::Returned,
        ]);
    }

    public function scrap(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MovementType::Scrapped,
        ]);
    }

    public function markLost(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => MovementType::Lost,
        ]);
    }
}
