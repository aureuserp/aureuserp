<?php

return [
    'form' => [
        'value'                  => 'Value',
        'due'                    => 'Due',
        'delay-due'              => 'Delay Due',
        'delay-type'             => 'Delay Type',
        'days-on-the-next-month' => 'Days on the next month',
        'days'                   => 'Days',
        'payment-term'           => 'Payment Term',
    ],

    'table' => [
        'columns' => [
            'due'          => 'Due',
            'value'        => 'Value',
            'value-amount' => 'Value Amount',
            'after'        => 'After',
            'delay-type'   => 'Delay Type',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Payment Due Term updated',
                    'body'  => 'The payment due term has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Payment Due Term deleted',
                    'body'  => 'The payment due term has been deleted successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Payment Due Term created',
                    'body'  => 'The payment due term has been created successfully.',
                ],
            ],
        ],
    ],
];
