<?php

return [
    'navigation' => [
        'title' => 'Transferencias Internas',
        'group' => 'Transferencias',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transferencia interna eliminada',
                        'body'  => 'La transferencia interna ha sido eliminada exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la transferencia interna',
                        'body'  => 'La transferencia interna no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transferencias internas eliminadas',
                        'body'  => 'Las transferencias internas han sido eliminadas exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las transferencias internas',
                        'body'  => 'Las transferencias internas no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],
];
