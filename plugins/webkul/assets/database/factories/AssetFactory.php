<?php

namespace Webkul\Asset\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Asset\Enums\AssetCondition;
use Webkul\Asset\Enums\AssetStatus;
use Webkul\Asset\Models\Asset;
use Webkul\Asset\Models\AssetProduct;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetWarehouse;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    use HasCompanyDefault {
        configure as configureCompanyDefault;
    }

    protected $model = Asset::class;

    public function configure(): static
    {
        return $this->configureCompanyDefault()->afterMaking(function (Asset $asset): void {
            if (blank($asset->product_id)) {
                $asset->product_id = AssetProduct::withoutGlobalScopes()
                    ->where('company_id', $asset->company_id)
                    ->where('is_active', true)
                    ->value('id')
                    ?? AssetProduct::factory()->create(['company_id' => $asset->company_id])->id;
            }

            if ($asset->status === AssetStatus::Available && blank($asset->warehouse_id)) {
                $asset->warehouse_id = AssetWarehouse::withoutGlobalScopes()
                    ->where('company_id', $asset->company_id)
                    ->where('is_active', true)
                    ->value('id')
                    ?? AssetWarehouse::factory()->create(['company_id' => $asset->company_id])->id;
            }
        });
    }

    public function definition(): array
    {
        return [
            'product_id'          => null,
            'warehouse_id'        => null,
            'asset_tag'           => fake()->unique()->bothify('AST-#####'),
            'serial_no'           => fake()->unique()->bothify('SN-########'),
            'status'              => AssetStatus::Available,
            'condition'           => AssetCondition::New,
            'current_employee_id' => null,
            'current_site_id'     => null,
            'issued_at'           => null,
            'expected_return_at'  => null,
            'custody_note'        => null,
            'purchase_date'       => null,
            'warranty_until'      => null,
            'cost'                => null,
            'note'                => null,
            'creator_id'          => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function forProduct(AssetProduct $product): static
    {
        return $this->state(fn (array $attributes) => [
            'product_id' => $product->id,
        ]);
    }

    public function withoutSerial(): static
    {
        return $this->state(fn (array $attributes) => [
            'serial_no' => null,
        ]);
    }

    public function available(?AssetWarehouse $warehouse = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status'              => AssetStatus::Available,
            'warehouse_id'        => $warehouse?->id ?? $attributes['warehouse_id'] ?? null,
            'current_employee_id' => null,
            'current_site_id'     => null,
            'issued_at'           => null,
            'expected_return_at'  => null,
            'custody_note'        => null,
        ]);
    }

    public function heldByEmployee(?Employee $employee = null, ?AssetSite $site = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status'              => AssetStatus::Held,
            'warehouse_id'        => null,
            'current_employee_id' => $employee?->id ?? fn () => Employee::factory()->create([
                'company_id' => $attributes['company_id'] ?? null,
                'user_id'    => User::factory(),
                'is_active'  => true,
            ]),
            'current_site_id'     => $site?->id,
            'issued_at'           => now(),
            'expected_return_at'  => null,
            'custody_note'        => null,
        ]);
    }

    public function heldBySite(?AssetSite $site = null, ?Employee $employee = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status'              => AssetStatus::Held,
            'warehouse_id'        => null,
            'current_site_id'     => $site?->id ?? fn () => ($employee ? AssetSite::factory()->custodyZone() : AssetSite::factory()->assetLocation())->create([
                'company_id' => $attributes['company_id'] ?? null,
            ]),
            'current_employee_id' => $employee?->id,
            'issued_at'           => now(),
            'expected_return_at'  => null,
            'custody_note'        => null,
        ]);
    }

    public function atSite(?AssetSite $site = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status'              => AssetStatus::Held,
            'warehouse_id'        => null,
            'current_employee_id' => null,
            'current_site_id'     => $site?->id ?? fn () => AssetSite::factory()->assetLocation()->create([
                'company_id' => $attributes['company_id'] ?? null,
            ]),
            'issued_at'           => now(),
            'expected_return_at'  => null,
            'custody_note'        => null,
        ]);
    }

    public function scrapped(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'              => AssetStatus::Scrapped,
            'condition'           => AssetCondition::Damaged,
            'warehouse_id'        => null,
            'current_employee_id' => null,
            'current_site_id'     => null,
            'issued_at'           => null,
            'expected_return_at'  => null,
            'custody_note'        => null,
        ]);
    }

    public function lost(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'              => AssetStatus::Lost,
            'warehouse_id'        => null,
            'current_employee_id' => null,
            'current_site_id'     => null,
            'issued_at'           => null,
            'expected_return_at'  => null,
            'custody_note'        => null,
        ]);
    }
}
