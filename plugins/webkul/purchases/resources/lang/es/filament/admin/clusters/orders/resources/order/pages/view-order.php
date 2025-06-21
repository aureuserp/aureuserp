<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Orden Eliminada',
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
