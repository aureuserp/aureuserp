<?php

namespace Webkul\Account\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class InvoiceSummary extends Component
{
    #[Reactive]
    public $subtotal = 0;

    public $totalDiscount = 0;

    #[Reactive]
    public $totalTax = 0;

    #[Reactive]
    public $grandTotal = 0;

    public $amountTax = 0;

    public $rounding = 0;

    #[Reactive]
    public $currency = null;

    public function mount($currency, $subtotal = 0, $totalTax = 0, $grandTotal = 0, $rounding = 0)
    {
        $this->currency = $currency;
        $this->subtotal = $subtotal;
        $this->totalTax = $totalTax;
        $this->grandTotal = $grandTotal;
        $this->rounding = $rounding ?? 0;
        $this->amountTax = $totalTax;
    }

    public function render()
    {
        return view('accounts::livewire/invoice-summary', [
            'rounding'   => $this->rounding,
            'amountTax'  => $this->amountTax,
            'subtotal'   => $this->subtotal,
            'totalTax'   => $this->totalTax,
            'grandTotal' => $this->grandTotal,
        ]);
    }
}
