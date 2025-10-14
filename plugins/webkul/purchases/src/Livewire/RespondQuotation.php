<?php

namespace Webkul\Purchase\Livewire;

use Filament\Pages\SimplePage;
use Webkul\Purchase\Models\Order;

class RespondQuotation extends SimplePage
{
    protected string $view = 'purchases::livewire.respond-quotation';

    public int $order;

    public string $action;

    public function mount(): void
    {
        $order = Order::findOrFail($this->order);

        if ($this->action === 'accept') {
            $order->update([
                'mail_reception_confirmed' => true,
            ]);

            $order->addMessage([
                'causer_type' => $order->partner->getMorphClass(),
                'causer_id'   => $order->partner->id,
                'body'        => 'The RFQ has been acknowledged by vendor.',
                'type'        => 'comment',
            ]);
        } else {
            $order->update([
                'mail_reception_declined' => true,
            ]);

            $order->addMessage([
                'causer_type' => $order->partner->getMorphClass(),
                'causer_id'   => $order->partner->id,
                'body'        => 'The RFQ has been declined by vendor.',
                'type'        => 'comment',
            ]);
        }
    }

    public function getHeading(): string
    {
        if ($this->action === 'accept') {
            return 'Quotation Accepted';
        }

        return 'Quotation Declined';
    }
}
