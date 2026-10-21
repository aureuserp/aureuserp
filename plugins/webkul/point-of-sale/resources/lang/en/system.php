<?php

return [
    'unexpected-error' => 'Something went wrong on the server. Try again, and ask a manager if it keeps happening.',

    'products' => [
        'tip'      => 'Tips',
        'discount' => 'Discount',
    ],

    'picking' => [
        'operation-type-missing' => 'No stock operation type is configured for order :order, so nothing was moved.',
    ],

    'config' => [
        'terminal-journal' => 'Point of Sale',
    ],

    'order-workflow' => [
        'customer' => [
            'required-by-terminal'       => 'This point of sale requires a customer on every order.',
            'required-to-invoice'        => 'Select a customer before invoicing this order.',
            'required-to-ship'           => 'Select a customer before shipping this order later.',
            'shipping-address-required'  => 'The selected customer needs a delivery address before shipping this order later.',
            'required-by-payment-method' => 'The selected payment method requires a customer.',
        ],

        'mark-paid' => [
            'already-settled'          => 'Order :order has already been settled.',
            'insufficient-payment'     => 'Order :order is not fully paid.',
            'cash-method-required'     => 'Order :order cannot be overpaid because this terminal has no cash payment method configured.',
        ],

        'cancel' => [
            'not-draft' => 'Order :order can no longer be cancelled.',
        ],

        'split' => [
            'not-draft' => 'Order :order can no longer be split.',
        ],

        'tip' => [
            'not-enabled' => 'Enable tips and set a tip product on the point of sale first.',
        ],

        'refund' => [
            'not-refundable'    => 'Order :order cannot be refunded.',
            'exceeds-sold'      => 'The refunded quantity exceeds what was sold on :product.',
            'no-payment-method' => 'Terminal :order has no payment method to refund with.',
            'nothing-to-refund' => 'Select at least one line to refund.',
        ],
    ],

    'global-discount' => [
        'not-enabled' => 'Enable the global discount and set a discount product on the point of sale first.',
        'not-draft'   => 'Order :order can no longer be discounted.',
    ],

    'terminal-product-creator' => [
        'name-required'    => 'Give the product a name.',
        'defaults-missing' => 'Set up a unit of measure and a product category before creating products at the till.',
    ],

    'payment-method' => [
        'in-open-session' => 'Close the open sessions :sessions before deleting this payment method.',
    ],

    'payment-method-provisioner' => [
        'cash'         => 'Cash',
        'cash-journal' => 'Cash',
    ],

    'order-sync' => [
        'product-missing'         => 'A product on this order no longer exists. Remove it and try again.',
        'customer-missing'        => 'The customer on this order no longer exists. Choose another customer.',
        'table-missing'           => 'The table of this order no longer exists. Move the order to another table.',
        'price-list-missing'      => 'The pricelist of this order no longer exists.',
        'fiscal-position-missing' => 'The fiscal position of this order no longer exists.',
        'payment-method-missing'  => 'A payment method on this order no longer exists.',
        'refunded-order-missing'  => 'The order being refunded can no longer be found.',
        'customer-email-invalid'  => 'The customer email address is not valid.',
        'discount-invalid'        => 'Line discounts must be between 0 and 100%.',
    ],

    'order-processor' => [
        'price-locked'           => 'Price changes on :product need a manager.',
        'line-discount-disabled' => 'Line discounts are turned off on this point of sale.',
        'foreign-order'          => 'This order belongs to another register and cannot be changed here.',
        'foreign-line'           => 'An item on this order belongs to another order. Reload the till and try again.',
    ],

    'floor-plan' => [
        'tables-in-use' => 'Tables :tables still have open orders. Pay, release or move them first.',
    ],

    'session-preflight' => [
        'draft-orders' => 'Pay or cancel these orders before closing the session: :orders.',

        'journal' => [
            'missing'      => 'Set a sales journal on the point of sale.',
            'invalid-type' => 'The point of sale journal must be a sales journal.',
        ],

        'invoice-journal' => [
            'missing'      => 'Set an invoice journal on the point of sale.',
            'invalid-type' => 'The invoice journal must be a sales journal.',
        ],

        'receivable-account' => [
            'missing'          => 'Set a receivable account on the point of sale.',
            'invalid-type'     => 'The point of sale receivable account must be a receivable account.',
            'not-reconcilable' => 'The point of sale receivable account must allow reconciliation.',
            'deprecated'       => 'The point of sale receivable account is deprecated.',
        ],

        'payment-methods' => [
            'missing'               => 'Add at least one payment method to the point of sale.',
            'pay-later-unsupported' => 'Payment method :method has no journal and customer accounts are not supported yet.',
            'journal-missing'       => 'Set a journal on payment method :method.',
            'journal-company'       => 'The journal of :method belongs to another company.',
            'receivable-missing'    => 'Set a receivable account for :method.',
        ],

        'cash-journal' => [
            'multiple'             => 'Only one cash payment method is supported per point of sale.',
            'profit-loss-missing'  => 'Set profit and loss accounts on the cash journal :journal.',
        ],

        'taxes' => [
            'account-missing' => 'Tax :tax has a distribution line without an account.',
        ],

        'global-discount' => [
            'product-missing' => 'Set a discount product on the point of sale to use global discounts.',
        ],

        'cogs' => [
            'stock-output-missing' => 'Set a stock output account before enabling cost of goods sold.',
        ],
    ],

    'invoice-payer' => [
        'entry-reference' => 'Invoice payment for :order (:invoice) using :method',
    ],

    'session-closer' => [
        'entry-reference'                 => 'Point of sale session :session',
        'payment-difference-reference'    => 'Difference on :method for :session',
        'cogs-reference'                  => 'Cost of goods sold for :session',
        'sales-line'                      => 'Point of sale sales',
        'tax-line'                        => 'Point of sale taxes',
        'receivable-line'                 => 'Point of sale receivable',
        'invoice-receivable-line'         => 'Point of sale invoice receivable',
        'cash-line'                       => 'Point of sale cash',
        'cash-difference-line'            => 'Cash difference at closing',
        'cogs-line'                       => 'Cost of goods sold',
        'stock-output-line'               => 'Stock output',
        'balancing-line'                  => 'Difference at closing',
        'rounding-line'                   => 'Cash rounding',
        'unbalanced'                      => 'The closing entry is out of balance by :delta. Choose a balancing account to post it.',
        'income-account-missing'          => 'No income account resolved for :product.',
        'expense-account-missing'         => 'No expense account resolved for :product.',
        'cash-account-missing'            => 'The cash journal has no default account.',
        'cash-difference-account-missing' => 'The cash journal has no profit or loss account.',
        'stock-output-missing'            => 'Set a stock output account on the point of sale.',
        'line-account-missing'            => 'A closing entry line has no account.',
        'rounding-account-missing'        => 'The cash rounding has no profit or loss account.',
        'difference-exceeded'             => 'The maximum difference allowed is :amount. Please contact your manager to accept the closing difference.',
    ],

    'invoicer' => [
        'customer-required' => 'Order :order needs a customer before it can be invoiced.',
        'journal-missing'   => 'Set an invoice journal on the point of sale.',
        'session-closed'    => 'Order :order belongs to a closed session and is already accounted for in the closing entry.',
    ],

    'session-workflow' => [
        'open' => [
            'already-open' => 'A session is already open for :config.',
            'inactive'     => 'The :config register is inactive and cannot be opened.',
        ],

        'assert-open' => [
            'not-open' => 'Session :session is not open.',
        ],

        'assert-state' => [
            'invalid' => 'Session :session cannot move from :state.',
        ],

        'discard' => [
            'not-discardable' => 'Session :name has activity recorded against it and must be closed, not discarded.',
        ],

        'cash-movement' => [
            'invalid-amount' => 'A cash movement amount must be greater than zero.',
        ],
    ],
];
