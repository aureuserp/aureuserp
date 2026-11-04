<?php

namespace Webkul\Asset\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Asset\Enums\AssetSiteUsage;
use Webkul\Asset\Models\AssetSite;
use Webkul\Asset\Models\AssetSiteType;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<AssetSite>
 */
class AssetSiteFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = AssetSite::class;

    public function definition(): array
    {
        return [
            'parent_id'                     => null,
            'usage_type'                    => AssetSiteUsage::AssetLocation,
            'site_type_id'                  => fn () => AssetSiteType::query()->whereNull('company_id')->value('id')
                ?? AssetSiteType::factory()->create(['company_id' => null])->id,
            'name'                          => fake()->company().' Site',
            'code'                          => fake()->unique()->bothify('SITE-####'),
            'default_custodian_employee_id' => null,
            'is_active'                     => true,
            'note'                          => null,
            'creator_id'                    => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function assetLocation(): static
    {
        return $this->state(fn () => ['usage_type' => AssetSiteUsage::AssetLocation]);
    }

    public function custodyZone(): static
    {
        return $this->state(fn () => ['usage_type' => AssetSiteUsage::CustodyZone]);
    }

    public function withParent(AssetSite $parent): static
    {
        return $this->state(fn () => [
            'company_id'   => $parent->company_id,
            'parent_id'    => $parent->id,
            'site_type_id' => $parent->site_type_id,
            'usage_type'   => $parent->usage_type,
        ]);
    }
}
