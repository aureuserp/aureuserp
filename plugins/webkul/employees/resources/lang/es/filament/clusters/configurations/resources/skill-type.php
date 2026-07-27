<?php

return [
    'title' => 'Tipos de competencia',

    'navigation' => [
        'title' => 'Tipos de competencia',
        'group' => 'Empleado',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'             => 'Nombre',
                'name-placeholder' => 'Introducir el nombre del tipo de competencia',
                'color'            => 'Color',
                'status'           => 'Estado',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Tipo de competencia',
            'status'     => 'Estado',
            'color'      => 'Color',
            'skills'     => 'Competencias',
            'levels'     => 'Niveles',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'skill-levels' => 'Niveles de competencia',
            'skills'       => 'Competencias',
            'created-by'   => 'Creado por',
            'status'       => 'Estado',
            'updated-at'   => 'Actualizado el',
            'created-at'   => 'Creado el',
        ],

        'groups' => [
            'name'       => 'Tipo de competencia',
            'color'      => 'Color',
            'status'     => 'Estado',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de competencia restaurado',
                    'body'  => 'El tipo de competencia se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de competencia eliminado',
                    'body'  => 'El tipo de competencia se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipos de competencia restaurados',
                    'body'  => 'Los tipos de competencia se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipos de competencia eliminados',
                    'body'  => 'Los tipos de competencia se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tipos de competencia eliminados permanentemente',
                    'body'  => 'Los tipos de competencia se han eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tipos de competencia',
                    'body'  => 'Los tipos de competencia se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'   => 'Tipo de competencia',
                'color'  => 'Color',
                'status' => 'Estado',
            ],
        ],
    ],
];
