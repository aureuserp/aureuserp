<?php

return [
    'label'          => 'Return Products',
    'refunded-label' => 'Refunded',

    'form' => [
        'fields' => [
            'payment-method' => 'Refund Payment Method',
            'lines'          => 'Lines to refund',
            'selected'       => 'Refund',
            'product'        => 'Product',
            'refundable'     => 'Refundable',
            'quantity'       => 'Quantity',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Refund created',
            'body'  => 'A refund order has been created for the selected lines.',
        ],
    ],
];
