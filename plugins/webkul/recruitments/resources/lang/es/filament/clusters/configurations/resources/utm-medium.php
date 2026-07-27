<?php

return [
    'title' => 'Medios',

    'navigation' => [
        'title' => 'Medios',
        'group' => 'UTMs',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nombre',
            'name-placeholder' => 'Introducir el nombre del medio',
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
            'created-by' => 'Creado por',
            'updated-at' => 'Actualizado el',
            'created-at' => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Medio actualizado',
                    'body'  => 'El medio se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Medio eliminado',
                    'body'  => 'El medio se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Medios eliminados',
                    'body'  => 'Los medios se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Medio creado',
                    'body'  => 'El medio se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nombre',
    ],
];
