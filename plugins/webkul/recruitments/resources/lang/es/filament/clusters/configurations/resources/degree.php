<?php

return [
    'title' => 'Titulaciones',

    'navigation' => [
        'title' => 'Titulaciones',
        'group' => 'Candidaturas',
    ],

    'groups' => [
        'status'     => 'Estado',
        'created-by' => 'Creado por',
        'created-at' => 'Creado el',
        'updated-at' => 'Actualizado el',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nombre',
            'name-placeholder' => 'Introducir el nombre de la titulación',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nombre',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'name'       => 'Nombre',
            'employee'   => 'Empleado',
            'created-by' => 'Creado por',
            'updated-at' => 'Actualizado el',
            'created-at' => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Titulación actualizada',
                    'body'  => 'La titulación se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Titulación eliminada',
                    'body'  => 'La titulación se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Titulaciones eliminadas',
                    'body'  => 'Las titulaciones se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Titulación creada',
                    'body'  => 'La titulación se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nombre',
    ],
];
