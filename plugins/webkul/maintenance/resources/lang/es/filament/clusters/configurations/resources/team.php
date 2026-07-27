<?php

return [
    'navigation' => [
        'title' => 'Equipos',
    ],

    'form' => [
        'name'    => 'Nombre',
        'company' => 'Empresa',
        'users'   => 'Miembros del equipo',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'company'    => 'Empresa',
            'users'      => 'Miembros del equipo',
            'created-at' => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipo actualizado',
                    'body'  => 'El equipo se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Equipo restaurado',
                    'body'  => 'El equipo se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipo eliminado',
                    'body'  => 'El equipo se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Equipo eliminado de forma permanente',
                        'body'  => 'El equipo se ha eliminado de forma permanente correctamente.',
                    ],
                    'error' => [
                        'title' => 'No se pudo eliminar el equipo de forma permanente',
                        'body'  => 'El equipo está en uso y no se puede eliminar de forma permanente.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipos restaurados',
                    'body'  => 'Los equipos se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipos eliminados',
                    'body'  => 'Los equipos se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
