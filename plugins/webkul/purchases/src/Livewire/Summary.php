<?php

namespace Webkul\Purchase\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class Summary extends Component
{
    #[Reactive]
    public $products = [];

    #[Reactive]
    public $currency = null;

    public $subtotal = 0;

    public $totalDiscount = 0;

    public $totalTax = 0;

    public $grandTotal = 0;

    public function mount($products)
    {
        $this->products = $products ?? [];
    }

    public function render()
    {
        return view('purchases::livewire/summary', [
            'products' => $this->products,
        ]);
    }
}
