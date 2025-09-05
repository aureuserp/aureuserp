<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Imprimir',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Recepción Eliminada',
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
