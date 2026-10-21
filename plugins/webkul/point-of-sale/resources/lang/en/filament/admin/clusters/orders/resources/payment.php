<?php

return [
    'navigation' => [
        'title' => 'Payments',
        'group' => 'Point of Sale',
    ],

    'table' => [
        'columns' => [
            'order'           => 'Order',
            'session'         => 'Session',
            'payment-method'  => 'Method',
            'amount'          => 'Amount',
            'partner'         => 'Customer',
            'is-change'       => 'Change',
            'terminal-status' => 'Terminal Status',
            'paid-at'         => 'Paid At',
        ],

        'groups' => [
            'payment-method' => 'Method',
            'session'        => 'Session',
        ],

        'filters' => [
            'payment-method' => 'Method',
            'session'        => 'Session',
        ],
    ],
];
