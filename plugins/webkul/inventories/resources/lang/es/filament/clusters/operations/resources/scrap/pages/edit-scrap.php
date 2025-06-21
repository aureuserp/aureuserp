<?php

return [
    'notification' => [
        'title' => 'Desecho actualizado',
        'body'  => 'El desecho ha sido actualizado exitosamente.',
    ],

    'header-actions' => [
        'validate' => [
            'label' => 'Validar',

            'notification' => [
                'warning' => [
                    'title' => 'Stock insuficiente',
                    'body'  => 'El desecho no tiene suficiente stock para validar.',
                ],

                'success' => [
                    'title' => 'Desecho marcado como realizado',
                    'body'  => 'El desecho ha sido marcado como realizado exitosamente.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Desecho eliminado',
                    'body'  => 'El desecho ha sido eliminado exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudieron eliminar los desechos',
                    'body'  => 'Los desechos no se pueden eliminar porque están actualmente en uso.',
                ],
            ],
        ],
    ],
];
