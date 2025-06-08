<?php

return [
    'title' => 'Ubicaciones de Trabajo',

    'navigation' => [
        'title' => 'Ubicaciones de Trabajo',
        'group' => 'Empleado',
    ],

    'form' => [
        'name'            => 'Nombre',
        'company'         => 'Empresa',
        'location-type'   => 'Tipo de Ubicación',
        'location-number' => 'Número de Ubicación',
        'status'          => 'Estado',
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'name'            => 'Nombre',
            'status'          => 'Estado',
            'company'         => 'Empresa',
            'location-type'   => 'Tipo de Ubicación',
            'location-number' => 'Número de Ubicación',
            'deleted-at'      => 'Eliminado El',
            'created-by'      => 'Creado Por',
            'created-at'      => 'Creado El',
            'updated-at'      => 'Actualizado El',
        ],

        'filters' => [
            'name'            => 'Nombre',
            'status'          => 'Estado',
            'created-by'      => 'Creado Por',
            'company'         => 'Empresa',
            'location-number' => 'Número de Ubicación',
            'location-type'   => 'Tipo de Ubicación',
            'updated-at'      => 'Actualizado El',
            'created-at'      => 'Creado El',
        ],

        'groups' => [
            'name'          => 'Nombre',
            'status'        => 'Estado',
            'location-type' => 'Tipo de Ubicación',
            'company'       => 'Empresa',
            'created-by'    => 'Creado Por',
            'created-at'    => 'Creado El',
            'updated-at'    => 'Actualizado El',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ubicación de Trabajo actualizada',
                    'body'  => 'La ubicación de trabajo ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ubicación de Trabajo restaurada',
                    'body'  => 'La ubicación de trabajo ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ubicación de Trabajo eliminada',
                    'body'  => 'La ubicación de trabajo ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Ubicación de Trabajo eliminada permanentemente',
                    'body'  => 'La ubicación de trabajo ha sido eliminada permanentemente.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Ubicación de Trabajo creada',
                    'body'  => 'La Ubicación de Trabajo ha sido creada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ubicaciones de Trabajo eliminadas',
                    'body'  => 'Las ubicaciones de trabajo han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Ubicaciones de Trabajo eliminadas permanentemente',
                    'body'  => 'Las ubicaciones de trabajo han sido eliminadas permanentemente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Nombre',
        'company'         => 'Empresa',
        'location-type'   => 'Tipo de Ubicación',
        'location-number' => 'Número de Ubicación',
        'status'          => 'Estado',
    ],
];
