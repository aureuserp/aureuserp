<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Empresa',
                'country'            => 'País',
                'name'               => 'Nombre',
                'preceding-subtotal' => 'Subtotal Precedente',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Empresa',
            'country'            => 'País',
            'created-by'         => 'Creado Por',
            'name'               => 'Nombre',
            'preceding-subtotal' => 'Subtotal Precedente',
            'created-at'         => 'Creado El',
            'updated-at'         => 'Actualizado El',
        ],

        'groups' => [
            'name'       => 'Nombre',
            'company'    => 'Empresa',
            'country'    => 'País',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Grupo de Impuestos eliminado',
                        'body'  => 'El grupo de impuestos ha sido eliminado exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el Grupo de Impuestos',
                        'body'  => 'El grupo de impuestos no puede ser eliminado porque está en uso actualmente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Grupos de Impuestos eliminados',
                        'body'  => 'Los grupos de impuestos han sido eliminados exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los Grupos de Impuestos',
                        'body'  => 'Los grupos de impuestos no pueden ser eliminados porque están en uso actualmente.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company'            => 'Empresa',
                'country'            => 'País',
                'name'               => 'Nombre',
                'preceding-subtotal' => 'Subtotal Precedente',
            ],
        ],
    ],
];
