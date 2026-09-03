<?php

return [
    'label'         => 'Producir todo',
    'partial-label' => 'Producir',

    'modal' => [
        'consumption-warning' => [
            'heading'     => 'Advertencia de consumo',
            'description' => 'Algunos productos se consumieron en cantidad distinta a la esperada. ¿Desea validar la orden de fabricación con las cantidades actuales?',

            'form' => [
                'product'    => 'Producto',
                'to-consume' => 'A consumir',
                'consumed'   => 'Consumido',
                'uom'        => 'Unidad de medida',
            ],

            'actions' => [
                'confirm' => [
                    'label' => 'Confirmar',
                ],

                'set-quantities' => [
                    'label' => 'Establecer cantidades y confirmar',
                ],
            ],
        ],

        'produced-warning' => [
            'heading'     => 'La cantidad producida es distinta a la esperada',
            'description' => 'La cantidad producida es distinta a la esperada. ¿Desea confirmar la orden de fabricación con la cantidad actual?',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Orden de fabricación completada',
            'body'  => 'La orden de fabricación ha sido completada correctamente.',
        ],
    ],
];
