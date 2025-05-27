<?php

return [
    'navigation' => [
        'title' => 'Envíos Directos',
        'group' => 'Transferencias',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Envío directo eliminado',
                        'body'  => 'El envío directo ha sido eliminado exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el envío directo',
                        'body'  => 'El envío directo no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Envíos directos eliminados',
                        'body'  => 'Los envíos directos han sido eliminados exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los envíos directos',
                        'body'  => 'Los envíos directos no se pueden eliminar porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],
];
