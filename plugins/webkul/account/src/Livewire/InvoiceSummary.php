<?php

namespace Webkul\Account\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class InvoiceSummary extends Component
{
    #[Reactive]
    public $products = [];

    public $subtotal = 0;

    public $totalDiscount = 0;

    public $totalTax = 0;

    public $grandTotal = 0;

    public $amountTax = 0;

    #[Reactive]
    public $currency = null;

    public function mount($currency, $products)
    {
        $this->currency = $currency;

        $this->products = $products ?? [];
    }

    public function render()
    {
        return view('accounts::livewire/invoice-summary', [
            'products' => $this->products,
        ]);
    }
}
