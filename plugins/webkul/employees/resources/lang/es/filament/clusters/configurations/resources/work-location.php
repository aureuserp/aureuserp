<?php

return [
    'title' => 'Ubicaciones de trabajo',

    'navigation' => [
        'title' => 'Ubicaciones de trabajo',
        'group' => 'Empleado',
    ],

    'form' => [
        'name'            => 'Nombre',
        'company'         => 'Empresa',
        'location-type'   => 'Tipo de ubicación',
        'location-number' => 'Número de ubicación',
        'status'          => 'Estado',
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'name'            => 'Nombre',
            'status'          => 'Estado',
            'company'         => 'Empresa',
            'location-type'   => 'Tipo de ubicación',
            'location-number' => 'Número de ubicación',
            'deleted-at'      => 'Eliminado el',
            'created-by'      => 'Creado por',
            'created-at'      => 'Creado el',
            'updated-at'      => 'Actualizado el',
        ],

        'filters' => [
            'name'            => 'Nombre',
            'status'          => 'Estado',
            'created-by'      => 'Creado por',
            'company'         => 'Empresa',
            'location-number' => 'Número de ubicación',
            'location-type'   => 'Tipo de ubicación',
            'updated-at'      => 'Actualizado el',
            'created-at'      => 'Creado el',
        ],

        'groups' => [
            'name'          => 'Nombre',
            'status'        => 'Estado',
            'location-type' => 'Tipo de ubicación',
            'company'       => 'Empresa',
            'created-by'    => 'Creado por',
            'created-at'    => 'Creado el',
            'updated-at'    => 'Actualizado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Ubicación de trabajo actualizada',
                    'body'  => 'La ubicación de trabajo se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Ubicación de trabajo restaurada',
                    'body'  => 'La ubicación de trabajo se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ubicación de trabajo eliminada',
                    'body'  => 'La ubicación de trabajo se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Ubicación de trabajo eliminada permanentemente',
                    'body'  => 'La ubicación de trabajo se ha eliminado permanentemente correctamente.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Ubicación de trabajo creada',
                    'body'  => 'La ubicación de trabajo se ha creado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ubicaciones de trabajo eliminadas',
                    'body'  => 'Las ubicaciones de trabajo se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Ubicaciones de trabajo eliminadas permanentemente',
                    'body'  => 'Las ubicaciones de trabajo se han eliminado permanentemente correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Nombre',
        'company'         => 'Empresa',
        'location-type'   => 'Tipo de ubicación',
        'location-number' => 'Número de ubicación',
        'status'          => 'Estado',
    ],
];
