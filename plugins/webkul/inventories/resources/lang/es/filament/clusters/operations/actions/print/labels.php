<?php

return [
    'label' => 'Etiquetas',

    'form' => [
        'fields' => [
            'type'            => 'Tipo de Etiquetas',
            'quantity'        => 'Cantidad',
            'format'          => 'Formato',
            'layout'          => 'Diseño de Etiquetas',
            'quantity-type'   => 'Cantidad a Imprimir',
            'quantity'        => 'Cantidad',

            'quantity-type-options' => [
                'operation' => 'Cantidad de la Operación',
                'custom'    => 'Cantidad Personalizada',
                'per-slot'  => 'Una por lote/SN',
                'per-unit'  => 'Una por unidad',
            ],

            'type-options' => [
                'product' => 'Etiquetas de Producto',
                'lot'     => 'Etiquetas de Lote/SN',
            ],

            'format-options' => [
                'dymo'      => 'Dymo',
                '2x7_price' => '2x7 con precio',
                '4x7_price' => '4x7 con precio',
                '4x12'      => '4x12',
                '4x12_price' => '4x12 con precio',
            ],
        ],
    ],
];
