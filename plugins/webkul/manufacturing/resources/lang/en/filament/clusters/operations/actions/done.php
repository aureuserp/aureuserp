<?php

return [
    'label'         => 'Produce All',
    'partial-label' => 'Produce',

    'modal' => [
        'consumption-warning' => [
            'heading'     => 'Consumption Warning',
            'description' => 'Some products consumed different quantity than expected. Do you want to validate the manufacturing order with the current quantities?',

            'form' => [
                'product'    => 'Product',
                'to-consume' => 'To Consume',
                'consumed'   => 'Consumed',
                'uom'        => 'Unit of Measure',
            ],

            'actions' => [
                'confirm' => [
                    'label' => 'Confirm',
                ],

                'set-quantities' => [
                    'label' => 'Set Quantities and Confirm',
                ],
            ],
        ],

        'produced-warning' => [
            'heading'     => 'Produced is different than expected',
            'description' => 'The quantity produced is different than expected. Do you want to confirm the manufacturing order with the current quantity?',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Manufacturing order completed',
            'body'  => 'The manufacturing order has been completed successfully.',
        ],
    ],
];
