<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Recepción eliminada',
                    'body'  => 'La recepción ha sido eliminada correctamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar la recepción',
                    'body'  => 'La recepción no puede eliminarse porque está en uso actualmente.',
                ],
            ],
        ],
    ],
];
