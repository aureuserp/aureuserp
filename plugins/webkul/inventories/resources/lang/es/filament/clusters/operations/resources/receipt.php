<?php

return [
    'navigation' => [
        'title' => 'Recepciones',
        'group' => 'Transferencias',
    ],

    'global-search' => [
        'partner' => 'Contacto',
        'origin'  => 'Origen',
    ],

    'table' => [
        'actions' => [
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

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Recepciones eliminadas',
                        'body'  => 'Las recepciones han sido eliminadas correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las recepciones',
                        'body'  => 'Las recepciones no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],
];
