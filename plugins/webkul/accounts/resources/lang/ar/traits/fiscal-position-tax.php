<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'Tax Source',
            'tax-destination' => 'Tax Destination',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'Tax Source',
            'tax-destination' => 'Tax Destination',
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

    'infolist' => [
        'entries' => [
            'tax-source'      => 'Tax Source',
            'tax-destination' => 'Tax Destination',
        ],
    ],
];
