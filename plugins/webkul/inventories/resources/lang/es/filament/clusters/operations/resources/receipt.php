<?php

return [
    'navigation' => [
        'title' => 'Recepciones',
        'group' => 'Transferencias',
    ],

    'table' => [
        'actions' => [
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

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Recepciones eliminadas',
                        'body'  => 'Las recepciones han sido eliminadas exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las recepciones',
                        'body'  => 'Las recepciones no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],
];
