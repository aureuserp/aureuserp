<?php

return [
    'notification' => [
        'title' => 'Merma actualizada',
        'body'  => 'La merma ha sido actualizada correctamente.',
    ],

    'header-actions' => [
        'validate' => [
            'label' => 'Validar',

            'notification' => [
                'warning' => [
                    'title' => 'Existencias insuficientes',
                    'body'  => 'La merma tiene existencias insuficientes para validar.',
                ],

                'success' => [
                    'title' => 'Merma marcada como realizada',
                    'body'  => 'La merma ha sido marcada como realizada correctamente.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Merma eliminada',
                    'body'  => 'La merma ha sido eliminada correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudieron eliminar las mermas',
                    'body'  => 'Las mermas no pueden eliminarse porque están en uso.',
                ],
            ],
        ],
    ],
];
