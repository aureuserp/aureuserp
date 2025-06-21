<?php

return [
    'form' => [
        'value'                  => 'Valor',
        'due'                    => 'Vencimiento',
        'delay-due'              => 'Retraso de Vencimiento',
        'delay-type'             => 'Tipo de Retraso',
        'days-on-the-next-month' => 'Días del próximo mes',
        'days'                   => 'Días',
        'payment-term'           => 'Condición de Pago',
    ],

    'table' => [
        'columns' => [
            'due'          => 'Vencimiento',
            'value'        => 'Valor',
            'value-amount' => 'Monto del Valor',
            'after'        => 'Después de',
            'delay-type'   => 'Tipo de Retraso',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Término de vencimiento de pago actualizado',
                    'body'  => 'El término de vencimiento de pago ha sido actualizado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Término de vencimiento de pago eliminado',
                    'body'  => 'El término de vencimiento de pago ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Término de vencimiento de pago creado',
                    'body'  => 'El término de vencimiento de pago ha sido creado exitosamente.',
                ],
            ],
        ],
    ],
];
