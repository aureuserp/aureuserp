<?php

return [
    'notification' => [
        'title' => 'Orden actualizada',
        'body'  => 'La orden ha sido actualizada exitosamente.',
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
                    'title' => 'Orden eliminada',
                    'body'  => 'La orden ha sido eliminada exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar la orden',
                    'body'  => 'La orden no puede ser eliminada porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
