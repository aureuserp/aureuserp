<?php

namespace Webkul\Purchase\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\PluginManager\Package;
use Webkul\Product\Models\Product;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\QtyReceivedMethod;
use Webkul\Purchase\Models\OrderLine;
use Webkul\Security\Models\User;

/**
 * @extends Factory<OrderLine>
 */
class OrderLineFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = OrderLine::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $productQty = fake()->randomFloat(2, 1, 100);
        $priceUnit = fake()->randomFloat(2, 10, 1000);
        $priceSubtotal = round($productQty * $priceUnit, 2);

        return [
            'name'                => fake()->words(3, true),
            'state'               => OrderState::DRAFT->value,
            'qty_received_method' => Package::isPluginInstalled('inventories')
                ? QtyReceivedMethod::STOCK_MOVE
                : QtyReceivedMethod::MANUAL,
            'product_id'          => Product::factory(),
            'planned_at'          => now()->addDays(7),
            'product_qty'         => $productQty,
            'product_uom_qty'     => $productQty,
            'price_unit'          => $priceUnit,
            'price_subtotal'      => $priceSubtotal,
            'price_tax'           => 0,
            'price_total'         => $priceSubtotal,
            'price_total_cc'      => $priceSubtotal,
            'discount'            => 0,
            'qty_invoiced'        => 0,
            'qty_received'        => 0,
            'qty_received_manual' => 0,
            'qty_to_invoice'      => 0,
            'creator_id'          => User::query()->value('id') ?? User::factory(),
        ];
    }
}
