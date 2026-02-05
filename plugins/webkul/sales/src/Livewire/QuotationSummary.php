<?php

namespace Webkul\Sale\Livewire;

use Livewire\Component;

class QuotationSummary extends Component
{
    public $subtotal = 0;

    public $totalDiscount = 0;

    public $totalTax = 0;

    public $grandTotal = 0;

    public $amountTax = 0;

    public $margin = 0;

    public $marginPercentage = 0;

    public $enableMargin = false;

    public $currency = null;

    protected $listeners = ['itemUpdated' => 'refreshSummary'];

    public function refreshSummary($totals)
    {
        $this->subtotal = $totals['subtotal'];
        $this->totalTax = $totals['totalTax'];
        $this->grandTotal = $totals['grandTotal'];
        $this->amountTax = $totals['totalTax'];
        $this->margin = $totals['margin'] ?? 0;
        $this->marginPercentage = $totals['marginPercentage'] ?? 0;
    }

    public function render()
    {
        return view('sales::livewire/quotation-summary');
    }
}
