<?php

return [
    'navigation' => [
        'title' => 'Entregas',
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
                        'title' => 'Entrega eliminada',
                        'body'  => 'La entrega ha sido eliminada correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la entrega',
                        'body'  => 'La entrega no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Entregas eliminadas',
                        'body'  => 'Las entregas han sido eliminadas correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las entregas',
                        'body'  => 'Las entregas no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],
];
