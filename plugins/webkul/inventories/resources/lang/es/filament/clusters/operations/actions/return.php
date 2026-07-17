<?php

return [
    'label' => 'Devolver',

    'modal' => [
        'form' => [
            'columns' => [
                'product'  => 'Producto',
                'quantity' => 'Cantidad',
                'uom'      => 'UOM',
            ],
        ],
    ],

    'notification' => [
        'no-products' => [
            'body' => 'No hay productos para devolver (solo se pueden devolver líneas en estado Realizado que aún no hayan sido devueltas completamente).',
        ],
        'no-quantities' => [
            'body' => 'Especifique al menos una cantidad distinta de cero.',
        ],
    ],
];
