<?php

return [
    'navigation' => [
        'title' => 'Transferencias internas',
        'group' => 'Transferencias',
    ],

    'global-search' => [
        'origin' => 'Origen',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transferencia interna eliminada',
                        'body'  => 'La transferencia interna ha sido eliminada correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la transferencia interna',
                        'body'  => 'La transferencia interna no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transferencias internas eliminadas',
                        'body'  => 'Las transferencias internas han sido eliminadas correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las transferencias internas',
                        'body'  => 'Las transferencias internas no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],
];
