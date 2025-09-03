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

    public function mount($products, $currency = null)

    {
        $this->products = $products ?? [];

        $this->currency = $currency;
    }

    public function render()
    {
        return view('purchases::livewire/summary', [
            'products' => $this->products,
            'currency' => $this->currency,
        ]);
    }
}
