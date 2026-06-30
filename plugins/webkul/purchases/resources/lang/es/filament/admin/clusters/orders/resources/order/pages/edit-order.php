<?php

return [
    'notification' => [
        'title' => 'Pedido actualizado',
        'body'  => 'El pedido se ha actualizado correctamente.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Confirmar',
        ],

        'close' => [
            'label' => 'Cerrar',
        ],

        'cancel' => [
            'label' => 'Cancelar',
        ],

        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Pedido eliminado',
                    'body'  => 'El pedido se ha eliminado correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el pedido',
                    'body'  => 'El pedido no se puede eliminar porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
