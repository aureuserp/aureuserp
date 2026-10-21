<?php

namespace Webkul\PointOfSale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Warehouse;
use Webkul\PointOfSale\Enums\PickingPolicy;
use Webkul\PointOfSale\Enums\TaxDisplay;
use Webkul\PointOfSale\Models\Config;
use Webkul\Security\Models\User;
use Webkul\Support\Database\Factories\Concerns\HasCompanyDefault;

/**
 * @extends Factory<Config>
 */
class ConfigFactory extends Factory
{
    use HasCompanyDefault;

    protected $model = Config::class;

    public function definition(): array
    {
        return [
            'name'                 => fake()->unique()->company().' Shop',
            'tax_display'          => TaxDisplay::SUBTOTAL,
            'picking_policy'       => PickingPolicy::DIRECT,
            'is_active'            => true,
            'warehouse_id'         => Warehouse::query()->value('id') ?? Warehouse::factory(),
            'creator_id'           => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function restaurant(): static
    {
        return $this->state(fn () => ['is_restaurant' => true]);
    }
}
