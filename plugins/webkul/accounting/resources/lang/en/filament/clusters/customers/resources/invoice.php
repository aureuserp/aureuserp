<?php

return [
    'title' => 'Invoice',

    'navigation' => [
        'title' => 'Invoices',
    ],

    'global-search' => [
        'customer' => 'Customer',
        'date'     => 'Date',
        'due-date' => 'Due Date',
        'amount'   => 'Amount',
    ],

    'form' => [
        'tabs' => [
            'invoice-lines' => [
                'repeater' => [
                    'products' => [
                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Open product',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
