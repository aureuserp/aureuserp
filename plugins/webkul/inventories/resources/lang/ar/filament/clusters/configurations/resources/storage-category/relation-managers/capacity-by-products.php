<?php

return [
    'title' => 'Capacity By Products',

    'form' => [
        'product' => 'Product',
        'qty'     => 'Quantity',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Add Product Capacity',

                'notification' => [
                    'title' => 'Product Capacity created',
                    'body'  => 'The product capacity been added successfully.',
                ],
            ],
        ],

        'columns' => [
            'product' => 'Product',
            'qty'     => 'Quantity',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Product Capacity updated',
                    'body'  => 'The product capacity has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Product Capacity deleted',
                    'body'  => 'The product capacity has been deleted successfully.',
                ],
            ],
        ],
    ],
];
