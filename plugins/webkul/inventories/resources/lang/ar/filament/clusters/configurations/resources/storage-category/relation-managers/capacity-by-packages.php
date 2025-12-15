<?php

return [
    'title' => 'Capacity By Packages',

    'form' => [
        'package-type' => 'Package Type',
        'qty'          => 'Quantity',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Add Package Type Capacity',

                'notification' => [
                    'title' => 'Package Type Capacity created',
                    'body'  => 'The package type capacity been added successfully.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'Package Type',
            'qty'          => 'Quantity',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Package Type Capacity updated',
                    'body'  => 'The package type capacity has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Package Type Capacity deleted',
                    'body'  => 'The package type capacity has been deleted successfully.',
                ],
            ],
        ],
    ],
];
