<?php

return [
    'label' => 'Imprimir etiquetas',

    'form' => [
        'fields' => [
            'quantity'      => 'Cantidad',
            'format'        => 'Formato',
            'quantity-type' => 'Cantidad a imprimir',

            'quantity-type-options' => [
                'operation' => 'Cantidad de operación',
                'custom'    => 'Cantidad personalizada',
            ],

            'format-options' => [
                'dymo'       => 'Dymo',
                '2x7_price'  => '2x7 con precio',
                '4x7_price'  => '4x7 con precio',
                '4x12'       => '4x12',
                '4x12_price' => '4x12 con precio',
            ],
        ],
    ],
];
