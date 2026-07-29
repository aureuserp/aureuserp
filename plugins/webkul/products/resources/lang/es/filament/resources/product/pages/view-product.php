<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir etiquetas',

            'form' => [
                'fields' => [
                    'quantity' => 'Número de etiquetas',
                    'format'   => 'Formato',

                    'format-options' => [
                        'dymo'       => 'Dymo',
                        '2x7_price'  => '2x7 con precio',
                        '4x7_price'  => '4x7 con precio',
                        '4x12'       => '4x12',
                        '4x12_price' => '4x12 con precio',
                    ],
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Producto eliminado',
                'body'  => 'El producto ha sido eliminado correctamente.',
            ],
        ],
    ],
];
