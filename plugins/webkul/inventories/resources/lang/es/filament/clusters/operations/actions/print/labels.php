<?php

return [
    'label' => 'Etiquetas',

    'form' => [
        'fields' => [
            'type'          => 'Tipo de etiquetas',
            'quantity'      => 'Cantidad',
            'format'        => 'Formato',
            'layout'        => 'Diseño de etiquetas',
            'quantity-type' => 'Cantidad a imprimir',
            'quantity'      => 'Cantidad',

            'quantity-type-options' => [
                'operation' => 'Cantidad de operación',
                'custom'    => 'Cantidad personalizada',
                'per-slot'  => 'Una por lote/NS',
                'per-unit'  => 'Una por unidad',
            ],

            'type-options' => [
                'product' => 'Etiquetas de producto',
                'lot'     => 'Etiquetas de lote/NS',
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
