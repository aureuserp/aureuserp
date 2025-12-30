<?php

return [
    'title' => 'Manage Taxes',

    'form' => [
        'default-taxes' => [
            'label'       => 'Default Taxes',
            'helper-text' => 'Default will be applied on products if no tax is selected',
        ],

        'sales-tax' => [
            'label' => 'Sales Tax',
        ],

        'purchase-tax' => [
            'label' => 'Purchase Tax',
        ],

        'prices' => [
            'label' => 'Prices',
        ],

        'rounding-method' => [
            'label'       => 'Rounding Method',
            'helper-text' => 'Method used to round tax amounts',

            'options' => [
                'round-per-line' => 'Round Per Line',
                'round-globally' => 'Round Globally',
            ],
        ],

        'fiscal-country' => [
            'label' => 'Fiscal Country',
        ],
    ],
];
