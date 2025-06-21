<?php

return [
    'title' => 'Tipos de Habilidades',

    'navigation' => [
        'title' => 'Tipos de Habilidades',
        'group' => 'Empleado',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'   => 'Nombre',
                'color'  => 'Color',
                'status' => 'Estado',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Tipo de Habilidad',
            'status'     => 'Estado',
            'color'      => 'Color',
            'skills'     => 'Habilidades',
            'levels'     => 'Niveles',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'skill-levels' => 'Niveles de Habilidad',
            'skills'       => 'Habilidades',
            'created-by'   => 'Creado Por',
            'status'       => 'Estado',
            'updated-at'   => 'Actualizado El',
            'created-at'   => 'Creado El',
        ],

        'groups' => [
            'name'       => 'Tipo de Habilidad',
            'color'      => 'Color',
            'status'     => 'Estado',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de Habilidad restaurado',
                    'body'  => 'El Tipo de Habilidad ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de Habilidad eliminado',
                    'body'  => 'El Tipo de Habilidad ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipos de Habilidades restaurados',
                    'body'  => 'Los Tipos de Habilidades han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipos de Habilidades eliminados',
                    'body'  => 'Los Tipos de Habilidades han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tipos de Habilidades eliminados permanentemente',
                    'body'  => 'Los Tipos de Habilidades han sido eliminados permanentemente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tipos de Habilidades',
                    'body'  => 'Los Tipos de Habilidades han sido creados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'   => 'Tipo de Habilidad',
                'color'  => 'Color',
                'status' => 'Estado',
            ],
        ],
    ],
];
