<?php

return [
    'navigation' => [
        'title' => 'Entregas',
        'group' => 'Transferencias',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Entrega eliminada',
                        'body'  => 'La entrega ha sido eliminada exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la entrega',
                        'body'  => 'La entrega no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Entregas eliminadas',
                        'body'  => 'Las entregas han sido eliminadas exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las entregas',
                        'body'  => 'Las entregas no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],
];
