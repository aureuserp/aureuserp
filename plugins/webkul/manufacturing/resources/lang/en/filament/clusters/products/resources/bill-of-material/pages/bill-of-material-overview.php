<?php

return [
    'navigation' => [
        'title' => 'Overview',
    ],

    'title' => 'Overview',

    'heading' => 'Overview',

    'filters' => [
        'quantity' => 'Quantity',
        'variant'  => 'Variant',
    ],

    'summary' => [
        'free-to-use' => 'Free to Use',
        'on-hand'     => 'On Hand',
        'total-cost'  => 'Total Cost',
    ],

    'table' => [
        'columns' => [
            'product'      => 'Product',
            'quantity'     => 'Quantity',
            'lead-time'    => 'Lead Time',
            'route'        => 'Route',
            'bom-cost'     => 'BoM Cost',
            'product-cost' => 'Product Cost',
        ],
        'sections' => [
            'operations' => 'Operations',
        ],
        'rows' => [
            'days'    => 'days',
            'minutes' => 'Minutes',
        ],
        'footer' => [
            'unit-cost' => 'Unit Cost',
        ],
    ],

    'by-products' => [
        'title'   => 'By-products',
        'columns' => [
            'product'  => 'By-product',
            'quantity' => 'Quantity',
            'uom'      => 'Unit of Measure',
        ],
    ],
];
