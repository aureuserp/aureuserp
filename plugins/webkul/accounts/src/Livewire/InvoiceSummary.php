<?php

namespace Webkul\Account\Livewire;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class InvoiceSummary extends Component
{
    public $subtotal = 0;

    public $totalDiscount = 0;

    public $totalTax = 0;

    public $grandTotal = 0;

    public $amountTax = 0;

    public $rounding = 0;

    public $currency = null;

    protected $listeners = ['itemUpdated' => 'refreshSummary'];

    public function refreshSummary($totals)
    {
        $this->subtotal = $totals['subtotal'];
        $this->totalTax = $totals['totalTax'];
        $this->grandTotal = $totals['grandTotal'];
        $this->amountTax = $totals['totalTax'];
        $this->rounding = $totals['rounding'];
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
