<?php

namespace Webkul\Purchase\Livewire;

use Livewire\Component;
use Webkul\Support\Models\Currency;

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

        if (array_key_exists('currency_id', $totals) && $totals['currency_id']) {
            $this->currency = Currency::find($totals['currency_id']);
        }
    }

    public function render()
    {
        return view('purchases::livewire/order-summary');
    }
}
