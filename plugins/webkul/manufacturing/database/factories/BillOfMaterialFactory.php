<?php

namespace Webkul\Manufacturing\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\OperationType;
use Webkul\Manufacturing\Enums\BillOfMaterialConsumption;
use Webkul\Manufacturing\Enums\BillOfMaterialReadyToProduce;
use Webkul\Manufacturing\Enums\BillOfMaterialType;
use Webkul\Manufacturing\Models\BillOfMaterial;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

/**
 * @extends Factory<BillOfMaterial>
 */
class BillOfMaterialFactory extends Factory
{
    protected $model = BillOfMaterial::class;

    public function definition(): array
    {
        return [
            'code'                         => strtoupper(fake()->bothify('BOM-####')),
            'type'                         => BillOfMaterialType::NORMAL,
            'ready_to_produce'             => BillOfMaterialReadyToProduce::ALL_AVAILABLE,
            'consumption'                  => BillOfMaterialConsumption::WARNING,
            'quantity'                     => fake()->randomFloat(4, 1, 25),
            'allow_operation_dependencies' => false,
            'produce_delay'                => 0,
            'days_to_prepare_mo'           => 0,
            'product_id'                   => Product::query()->value('id') ?? Product::factory(),
            'uom_id'                       => UOM::query()->value('id') ?? UOM::factory(),
            'operation_type_id'            => OperationType::query()->value('id') ?? OperationType::factory(),
            'company_id'                   => Company::query()->value('id') ?? Company::factory(),
            'creator_id'                   => User::query()->value('id') ?? User::factory(),
        ];
    }

    public function strict(): static
    {
        return $this->state(fn () => ['consumption' => BillOfMaterialConsumption::STRICT]);
    }

    public function warning(): static
    {
        return $this->state(fn () => ['consumption' => BillOfMaterialConsumption::WARNING]);
    }

    public function flexible(): static
    {
        return $this->state(fn () => ['consumption' => BillOfMaterialConsumption::FLEXIBLE]);
    }

    public function phantom(): static
    {
        return $this->state(fn () => ['type' => BillOfMaterialType::PHANTOM]);
    }
}
