<?php

namespace Webkul\Sale\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Sale\Models\AdvancedPaymentInvoice;
use Webkul\Sale\Models\AdvancedPaymentInvoiceOrderSale;
use Webkul\Sale\Models\Order;

/**
 * @extends Factory<AdvancedPaymentInvoiceOrderSale>
 */
class AdvancedPaymentInvoiceOrderSaleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdvancedPaymentInvoiceOrderSale::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'advance_payment_invoice_id' => AdvancedPaymentInvoice::factory(),
            'order_id'                   => Order::factory(),
        ];
    }
}
