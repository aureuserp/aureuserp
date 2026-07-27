<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Empresa',
                'country'            => 'País',
                'name'               => 'Nombre',
                'preceding-subtotal' => 'Subtotal anterior',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Empresa',
            'country'            => 'País',
            'created-by'         => 'Creado por',
            'name'               => 'Nombre',
            'preceding-subtotal' => 'Subtotal anterior',
            'created-at'         => 'Creado el',
            'updated-at'         => 'Actualizado el',
        ],

        'groups' => [
            'name'       => 'Nombre',
            'company'    => 'Empresa',
            'country'    => 'País',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Grupo de impuestos eliminado',
                        'body'  => 'El grupo de impuestos se ha eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar el grupo de impuestos',
                        'body'  => 'El grupo de impuestos no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Grupos de impuestos eliminados',
                        'body'  => 'Los grupos de impuestos se han eliminado correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar los grupos de impuestos',
                        'body'  => 'Los grupos de impuestos no se pueden eliminar porque están actualmente en uso.',
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
                'preceding-subtotal' => 'Subtotal anterior',
            ],
        ],
    ],
];
