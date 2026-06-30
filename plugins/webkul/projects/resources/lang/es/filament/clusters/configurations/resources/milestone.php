<?php

return [
    'navigation' => [
        'title' => 'Hitos',
    ],

    'form' => [
        'name'         => 'Nombre',
        'deadline'     => 'Fecha límite',
        'is-completed' => 'Completado',
        'project'      => 'Proyecto',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nombre',
            'deadline'     => 'Fecha límite',
            'is-completed' => 'Completado',
            'completed-at' => 'Completado el',
            'project'      => 'Proyecto',
            'creator'      => 'Creador',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'groups' => [
            'name'         => 'Nombre',
            'is-completed' => 'Completado',
            'project'      => 'Proyecto',
            'created-at'   => 'Creado el',
        ],

        'filters' => [
            'is-completed' => 'Completado',
            'project'      => 'Proyecto',
            'creator'      => 'Creador',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hito actualizado',
                    'body'  => 'El hito se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hito eliminado',
                    'body'  => 'El hito se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hitos eliminados',
                    'body'  => 'Los hitos se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
