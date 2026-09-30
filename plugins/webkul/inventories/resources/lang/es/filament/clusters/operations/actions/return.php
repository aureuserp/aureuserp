<?php

return [
    'label' => 'Devolver',

    'modal' => [
        'form' => [
            'columns' => [
                'product'                 => 'Producto',
                'quantity'                => 'Cantidad',
                'uom'                     => 'UOM',
                'excess-quantity-tooltip' => 'La cantidad a devolver es mayor que la cantidad devolvible restante.',
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
        'excess-quantity' => [
            'body' => 'La cantidad a devolver para :product no puede ser mayor que la cantidad devolvible restante (:quantity).',
        ],
    ],
];
