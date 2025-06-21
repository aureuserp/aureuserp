<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'Impuesto Origen',
            'tax-destination' => 'Impuesto Destino',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'Impuesto Origen',
            'tax-destination' => 'Impuesto Destino',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Término de Vencimiento de Pago actualizado', // Posiblemente debería ser 'Mapeo de Impuestos de Posición Fiscal actualizado'
                    'body'  => 'El término de vencimiento de pago ha sido actualizado exitosamente.', // Posiblemente debería ser 'El mapeo de impuestos de la posición fiscal ha sido actualizado exitosamente.'
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Término de Vencimiento de Pago eliminado', // Posiblemente debería ser 'Mapeo de Impuestos de Posición Fiscal eliminado'
                    'body'  => 'El término de vencimiento de pago ha sido eliminado exitosamente.', // Posiblemente debería ser 'El mapeo de impuestos de la posición fiscal ha sido eliminado exitosamente.'
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Término de Vencimiento de Pago creado', // Posiblemente debería ser 'Mapeo de Impuestos de Posición Fiscal creado'
                    'body'  => 'El término de vencimiento de pago ha sido creado exitosamente.', // Posiblemente debería ser 'El mapeo de impuestos de la posición fiscal ha sido creado exitosamente.'
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'tax-source'      => 'Impuesto Origen',
            'tax-destination' => 'Impuesto Destino',
        ],
    ],
];
