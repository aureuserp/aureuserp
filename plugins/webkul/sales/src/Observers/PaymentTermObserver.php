<?php

namespace Webkul\Sale\Observers;

use Exception;
use Webkul\Account\Models\PaymentTerm;
use Webkul\PluginManager\Package;
use Webkul\Sale\Models\Order;

class PaymentTermObserver
{
    public function deleting(PaymentTerm $paymentTerm): void
    {
        if (! Package::isPluginInstalled('sales')) {
            return;
        }

        $order = Order::withoutGlobalScopes()
            ->where('payment_term_id', $paymentTerm->id)
            ->first();

        if (! $order) {
            return;
        }

        throw new Exception(__('sales::observers/payment-term.in-use', [
            'payment_term' => $paymentTerm->name,
            'order'        => $order->name,
        ]));
    }
}
