<?php

return [
    'notification' => [
        'title' => 'Envío directo actualizado',
        'body'  => 'El envío directo ha sido actualizado correctamente.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Envío directo eliminado',
                    'body'  => 'El envío directo ha sido eliminado correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar el envío directo',
                    'body'  => 'El envío directo no puede eliminarse porque está en uso actualmente.',
                ],
            ],
        ],
    ],
];
