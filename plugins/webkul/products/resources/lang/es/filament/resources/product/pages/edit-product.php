<?php

return [
    'notification' => [
        'title' => 'Producto actualizado',
        'body'  => 'El producto ha sido actualizado exitosamente.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimir Etiquetas',

            'form' => [
                'fields' => [
                    'quantity' => 'Número de Etiquetas',
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
                'body'  => 'El producto ha sido eliminado exitosamente.',
            ],
        ],
    ],
];
