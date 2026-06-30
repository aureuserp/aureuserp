<?php

return [
    'form' => [
        'value'                  => 'Valor',
        'due'                    => 'Vencimiento',
        'delay-due'              => 'Vencimiento del plazo',
        'delay-type'             => 'Tipo de plazo',
        'days-on-the-next-month' => 'Días del mes siguiente',
        'days'                   => 'Días',
        'payment-term'           => 'Condición de pago',
    ],

    'table' => [
        'columns' => [
            'due'          => 'Vencimiento',
            'value'        => 'Valor',
            'value-amount' => 'Importe del valor',
            'after'        => 'Después',
            'delay-type'   => 'Tipo de plazo',
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
];
