<?php

return [
    'navigation' => [
        'title' => 'Envíos directos',
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
                        'title' => 'Envío directo eliminado',
                        'body'  => 'El envío directo ha sido eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el envío directo',
                        'body'  => 'El envío directo no puede eliminarse porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Envíos directos eliminados',
                        'body'  => 'Los envíos directos han sido eliminados correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los envíos directos',
                        'body'  => 'Los envíos directos no pueden eliminarse porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],
];
