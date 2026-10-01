<?php

namespace Webkul\Purchase\Observers;

use Exception;
use Webkul\Account\Models\PaymentTerm;
use Webkul\PluginManager\Package;
use Webkul\Purchase\Models\Order;

class PaymentTermObserver
{
    public function deleting(PaymentTerm $paymentTerm): void
    {
        if (! Package::isPluginInstalled('purchases')) {
            return;
        }

        $order = Order::withoutGlobalScopes()
            ->where('payment_term_id', $paymentTerm->id)
            ->first();

        if (! $order) {
            return;
        }

        throw new Exception(__('purchases::observers/payment-term.in-use', [
            'payment_term' => $paymentTerm->name,
            'order'        => $order->name,
        ]));
    }
}
