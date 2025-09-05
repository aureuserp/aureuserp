<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Envío Directo Eliminado',
                    'body'  => 'El envío directo ha sido eliminado exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el envío directo',
                    'body'  => 'El envío directo no se puede eliminar porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
