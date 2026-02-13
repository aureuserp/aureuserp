<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Inventory\Models\Route;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Partner\Models\Partner;
use Webkul\Product\Models\Packaging;
use Webkul\Product\Models\Product;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QtyDeliveredMethod;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Models\UOM;

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
        $quantity = $this->faker->randomFloat(2, 1, 100);
        $priceUnit = $this->faker->randomFloat(4, 10, 1000);
        $discount = 0;

        $priceSubtotal = $quantity * $priceUnit * (1 - $discount / 100);
        $priceTax = $priceSubtotal * 0.15;
        $priceTotal = $priceSubtotal + $priceTax;
        $technicalPriceUnit = $priceUnit;
        $purchasePrice = $priceUnit * 0.6;
        $margin = $priceSubtotal - ($purchasePrice * $quantity);
        $marginPercent = $priceSubtotal > 0 ? ($margin / $priceSubtotal) * 100 : 0;

        return [
            'sort'                         => $this->faker->numberBetween(1, 100),
            'state'                        => OrderState::DRAFT,
            'display_type'                 => null,
            'virtual_id'                   => null,
            'linked_virtual_id'            => null,
            'qty_delivered_method'         => QtyDeliveredMethod::STOCK_MOVE,
            'invoice_status'               => null,
            'analytic_distribution'        => null,
            'name'                         => $this->faker->sentence(3),
            'product_uom_qty'              => $quantity,
            'price_unit'                   => $priceUnit,
            'discount'                     => $discount,
            'price_subtotal'               => $priceSubtotal,
            'price_total'                  => $priceTotal,
            'price_reduce_taxexcl'         => $priceSubtotal / $quantity,
            'price_reduce_taxinc'          => $priceTotal / $quantity,
            'qty_delivered'                => 0,
            'qty_invoiced'                 => 0,
            'qty_to_invoice'               => $quantity,
            'untaxed_amount_invoiced'      => 0,
            'untaxed_amount_to_invoice'    => $priceSubtotal,
            'is_downpayment'               => false,
            'is_expense'                   => false,
            'technical_price_unit'         => $technicalPriceUnit,
            'price_tax'                    => $priceTax,
            'product_qty'                  => $quantity,
            'product_packaging_qty'        => null,
            'customer_lead'                => $this->faker->numberBetween(1, 30),
            'purchase_price'               => $purchasePrice,
            'margin'                       => $margin,
            'margin_percent'               => $marginPercent,
            'create_date'                  => $this->faker->dateTimeBetween('-30 days', 'now'),
            'write_date'                   => $this->faker->dateTimeBetween('-30 days', 'now'),
            'order_id'                     => Order::factory(),
            'company_id'                   => Company::factory(),
            'currency_id'                  => Currency::factory(),
            'order_partner_id'             => Partner::factory(),
            'salesman_id'                  => User::factory(),
            'product_id'                   => Product::factory(),
            'product_uom_id'               => UOM::factory(),
            'linked_sale_order_sale_id'    => null,
            'creator_id'                   => User::factory(),
            'warehouse_id'                 => null,
            'product_packaging_id'         => null,
            'route_id'                     => null,
        ];
    }

    /**
     * Indicate that the line is in draft state.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::DRAFT,
        ]);
    }

    /**
     * Indicate that the line is for a sale.
     */
    public function sale(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => OrderState::SALE,
        ]);
    }

    /**
     * Indicate that the line is a downpayment.
     */
    public function downpayment(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_downpayment' => true,
        ]);
    }

    /**
     * Indicate that the line is an expense.
     */
    public function expense(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_expense' => true,
        ]);
    }

    /**
     * Indicate that the line has a discount.
     */
    public function withDiscount(?float $discount = null): static
    {
        $discount = $discount ?? $this->faker->randomFloat(2, 5, 20);

        return $this->state(function (array $attributes) use ($discount) {
            $quantity = $attributes['product_uom_qty'];
            $priceUnit = $attributes['price_unit'];
            $priceSubtotal = $quantity * $priceUnit * (1 - $discount / 100);
            $priceTax = $priceSubtotal * 0.15;
            $priceTotal = $priceSubtotal + $priceTax;
            $purchasePrice = $attributes['purchase_price'];
            $margin = $priceSubtotal - ($purchasePrice * $quantity);
            $marginPercent = $priceSubtotal > 0 ? ($margin / $priceSubtotal) * 100 : 0;

            return [
                'discount'                  => $discount,
                'price_subtotal'            => $priceSubtotal,
                'price_total'               => $priceTotal,
                'price_reduce_taxexcl'      => $priceSubtotal / $quantity,
                'price_reduce_taxinc'       => $priceTotal / $quantity,
                'price_tax'                 => $priceTax,
                'untaxed_amount_to_invoice' => $priceSubtotal,
                'margin'                    => $margin,
                'margin_percent'            => $marginPercent,
            ];
        });
    }

    /**
     * Indicate that the line is partially delivered.
     */
    public function partiallyDelivered(): static
    {
        return $this->state(function (array $attributes) {
            $quantity = $attributes['product_uom_qty'];
            $deliveredQty = $quantity * 0.5;

            return [
                'qty_delivered' => $deliveredQty,
            ];
        });
    }

    /**
     * Indicate that the line is fully delivered.
     */
    public function fullyDelivered(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'qty_delivered' => $attributes['product_uom_qty'],
            ];
        });
    }

    /**
     * Indicate that the line has packaging.
     */
    public function withPackaging(): static
    {
        return $this->state(fn (array $attributes) => [
            'product_packaging_id'  => Packaging::factory(),
            'product_packaging_qty' => $this->faker->randomFloat(2, 1, 10),
        ]);
    }

    /**
     * Indicate that the line has a warehouse.
     */
    public function withWarehouse(): static
    {
        return $this->state(fn (array $attributes) => [
            'warehouse_id' => Warehouse::factory(),
        ]);
    }

    /**
     * Indicate that the line has a route.
     */
    public function withRoute(): static
    {
        return $this->state(fn (array $attributes) => [
            'route_id' => Route::factory(),
        ]);
    }
}
