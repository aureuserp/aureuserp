<?php

return [
    'locations' => [
        'pre-production'  => 'Preproducción',
        'post-production' => 'Posproducción',
    ],

    'operation-types' => [
        'pick-components'        => 'Recoger componentes',
        'store-finished-product' => 'Almacenar producto terminado',
        'manufacturing'          => 'Fabricación',
    ],

    'routes' => [
        'manufacture-one-step'    => ':warehouse: Fabricar (1 paso)',
        'manufacture-two-steps'   => ':warehouse: Recoger componentes y luego fabricar (2 pasos)',
        'manufacture-three-steps' => ':warehouse: Recoger componentes, fabricar y luego almacenar productos (3 pasos)',
    ],

    'rules' => [
        'stock-pre-production'      => ':code: Existencias → Preproducción',
        'pre-production-production' => ':code: Preproducción → Producción',
        'post-production-stock'     => ':code: Posproducción → Existencias',
    ],
];
