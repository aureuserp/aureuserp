<?php

namespace Webkul\Purchase\Livewire;

use Livewire\Component;

class OrderSummary extends Component
{
    public $subtotal = 0;

    public $totalDiscount = 0;

    public $totalTax = 0;

    public $grandTotal = 0;

    public $amountTax = 0;

    public $currency = null;

    protected $listeners = ['itemUpdated' => 'refreshSummary'];

    public function refreshSummary($totals)
    {
        $this->subtotal = $totals['subtotal'];
        $this->totalTax = $totals['totalTax'];
        $this->grandTotal = $totals['grandTotal'];
        $this->amountTax = $totals['totalTax'];
    }

    public function render()
    {
        return view('purchases::livewire/order-summary');
    }
}
