<?php

namespace Webkul\Crm\Livewire;

use Webkul\Account\Enums\MoveType;
use Webkul\Account\Models\Payment;
use Webkul\Crm\Filament\Clusters\Sales\Resources\PaymentResource;
use Webkul\Invoice\Livewire\InvoiceSummary;

class QuotationInvoiceSummary extends InvoiceSummary
{
    public function getResourceUrl($record): ?string
    {
        if ($record['move_type'] === MoveType::ENTRY && in_array(Payment::query()->find($record['account_payment_id'])?->partner_type, ['customer', 'company'], true)) {
            return PaymentResource::getUrl('view', ['record' => $record['account_payment_id']]);
        }

        return parent::getResourceUrl($record);
    }
}
