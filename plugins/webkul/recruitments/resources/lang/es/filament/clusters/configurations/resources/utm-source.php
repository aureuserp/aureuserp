<?php

return [
    'title' => 'Fuentes',

    'navigation' => [
        'title' => 'Fuentes',
        'group' => 'UTMs',
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
            'name-placeholder' => 'Introducir el nombre de la fuente',
            'status'           => 'Estado',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nombre',
            'status'     => 'Estado',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'name'       => 'Nombre',
            'created-by' => 'Creado por',
            'updated-at' => 'Actualizado el',
            'created-at' => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Fuente actualizada',
                    'body'  => 'La fuente se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Fuente eliminada',
                    'body'  => 'La fuente se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Fuentes eliminadas',
                    'body'  => 'Las fuentes se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Fuente creada',
                    'body'  => 'La fuente se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nombre',
    ],
];
