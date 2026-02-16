<?php

namespace Webkul\Account\Database\Factories;

use Webkul\Account\Models\Invoice;

/**
 * @extends MoveFactory<\Webkul\Account\Models\Invoice>
 */
class InvoiceFactory extends MoveFactory
{
    protected $model = Invoice::class;

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterMaking(function (Invoice $invoice) {
            //
        })->afterCreating(function (Invoice $invoice) {
            //
        })->invoice();
    }
}
