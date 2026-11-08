<?php

namespace Webkul\Asset\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Asset\Models\AssetSiteType;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<AssetSiteType>
 */
class AssetSiteTypeFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = AssetSiteType::class;

    public function definition(): array
    {
        return [
            'company_id' => null,
            'name'       => fake()->unique()->words(2, true).' Type',
            'icon'       => 'heroicon-m-map-pin',
            'color'      => '#3b82f6',
        ];
    }
}
