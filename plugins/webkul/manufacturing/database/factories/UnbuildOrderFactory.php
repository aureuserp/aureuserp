<?php

namespace Webkul\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Lot;
use Webkul\Manufacturing\Enums\UnbuildOrderState;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Manufacturing\Models\UnbuildOrder;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<UnbuildOrder>
 */
class UnbuildOrderFactory extends Factory
{
    protected $model = UnbuildOrder::class;

    public function definition(): array
    {
        return [
            'name'                    => strtoupper(fake()->bothify('UB-######')),
            'state'                   => UnbuildOrderState::DRAFT,
            'quantity'                => fake()->randomFloat(4, 1, 10),
            'product_id'              => Product::query()->value('id') ?? Product::factory(),
            'company_id'              => Company::query()->value('id') ?? Company::factory(),
            'uom_id'                  => UOM::query()->value('id') ?? UOM::factory(),
            'bill_of_material_id'     => BillOfMaterial::factory(),
            'manufacturing_order_id'  => null,
            'lot_id'                  => Lot::query()->value('id'),
            'location_id'             => Location::query()->value('id') ?? Location::factory(),
            'destination_location_id' => Location::query()->value('id') ?? Location::factory(),
            'creator_id'              => User::query()->value('id') ?? User::factory(),
        ];
    }
}
