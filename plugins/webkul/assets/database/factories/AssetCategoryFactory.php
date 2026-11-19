<?php

namespace Webkul\Asset\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Asset\Models\AssetCategory;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<AssetCategory>
 */
class AssetCategoryFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = AssetCategory::class;

    public function definition(): array
    {
        return [
            'company_id' => null,
            'parent_id'  => null,
            'name'       => fake()->unique()->words(2, true),
        ];
    }

    public function withParent(AssetCategory $parent): static
    {
        return $this->state(fn () => [
            'company_id' => $parent->company_id,
            'parent_id'  => $parent->id,
        ]);
    }
}
