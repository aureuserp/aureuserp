<?php

return [
    'notification' => [
        'title' => 'Acuerdo de compra actualizado',
        'body'  => 'El acuerdo de compra se ha actualizado correctamente.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Confirmar',

            'notification' => [
                'unable' => [
                    'title' => 'No se puede confirmar el acuerdo de compra',
                    'body'  => 'Agregar al menos una línea de producto antes de confirmar este acuerdo de compra.',
                ],
            ],
        ],

        'close' => [
            'label'        => 'Cerrar',
            'notification' => [
                'warning' => [
                    'title' => 'No se puede cerrar el acuerdo de compra',
                    'body'  => 'No se puede cerrar este acuerdo de compra porque algunas solicitudes de presupuesto relacionadas no están en estado Realizado o Cancelado.',
                ],
            ],
        ],

        'cancel' => [
            'label' => 'Cancelar',
        ],

        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'title' => 'Acuerdo de compra eliminado',
                'body'  => 'El acuerdo de compra se ha eliminado correctamente.',
            ],
        ],
    ],
];
