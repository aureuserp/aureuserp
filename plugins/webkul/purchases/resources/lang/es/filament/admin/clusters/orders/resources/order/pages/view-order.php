<?php

return [
    'header-actions' => [
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
