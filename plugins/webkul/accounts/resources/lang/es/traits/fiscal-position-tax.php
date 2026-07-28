<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'Impuesto de origen',
            'tax-destination' => 'Impuesto de destino',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'Impuesto de origen',
            'tax-destination' => 'Impuesto de destino',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Condición de pago actualizada',
                    'body'  => 'La condición de pago se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Condición de pago eliminada',
                    'body'  => 'La condición de pago se ha eliminado correctamente.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Condición de pago creada',
                    'body'  => 'La condición de pago se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'tax-source'      => 'Impuesto de origen',
            'tax-destination' => 'Impuesto de destino',
        ],
    ],
];
