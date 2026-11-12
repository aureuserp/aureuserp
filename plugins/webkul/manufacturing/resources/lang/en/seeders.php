<?php

return [
    'locations' => [
        'pre-production'  => 'Pre-Production',
        'post-production' => 'Post-Production',
    ],

    'operation-types' => [
        'pick-components'        => 'Pick Components',
        'store-finished-product' => 'Store Finished Product',
        'manufacturing'          => 'Manufacturing',
    ],

    'routes' => [
        'manufacture-one-step'    => ':warehouse: Manufacture (1 step)',
        'manufacture-two-steps'   => ':warehouse: Pick components and then manufacture (2 steps)',
        'manufacture-three-steps' => ':warehouse: Pick components, manufacture and then store products (3 steps)',
    ],

    'rules' => [
        'stock-pre-production'      => ':code: Stock → Pre-Production',
        'pre-production-production' => ':code: Pre-Production → Production',
        'post-production-stock'     => ':code: Post-Production → Stock',
    ],
];
