<?php

return [
    'notification' => [
        'title' => 'Recepción actualizada',
        'body'  => 'La recepción ha sido actualizada exitosamente.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Recepción eliminada',
                    'body'  => 'La recepción ha sido eliminada exitosamente.',
                ],

                'error' => [
                    'title' => 'No se pudo eliminar la recepción',
                    'body'  => 'La recepción no se puede eliminar porque está actualmente en uso.',
                ],
            ],
        ],
    ],
];
