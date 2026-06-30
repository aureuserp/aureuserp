<?php

return [
    'header-actions' => [
        'validate' => [
            'label' => 'Validar',

            'notification' => [
                'warning' => [
                    'title' => 'Stock insuficiente',
                    'body'  => 'La merma tiene stock insuficiente para validar.',
                ],

                'success' => [
                    'title' => 'Merma marcada como realizada',
                    'body'  => 'La merma se ha marcado como realizada correctamente.',
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
