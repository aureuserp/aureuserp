<?php

return [
    'title' => 'Tipos de Empleo',

    'navigation' => [
        'title' => 'Tipos de Empleo',
        'group' => 'Reclutamiento',
    ],

    'form' => [
        'fields' => [
            'name'    => 'Tipo de Empleo',
            'code'    => 'Código',
            'country' => 'País',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Tipo de Empleo',
            'code'       => 'Código',
            'country'    => 'País',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'name'       => 'Tipo de Empleo',
            'country'    => 'País',
            'created-by' => 'Creado Por',
            'updated-at' => 'Actualizado El',
            'created-at' => 'Creado El',
        ],

        'groups' => [
            'name'       => 'Tipo de Empleo',
            'country'    => 'País',
            'code'       => 'Código',
            'created-by' => 'Creado Por',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tipo de Empleo',
                    'body'  => 'El Tipo de Empleo ha sido editado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de Empleo eliminado',
                    'body'  => 'El Tipo de Empleo ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipos de Empleo eliminados',
                    'body'  => 'Los Tipos de Empleo han sido eliminados exitosamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tipos de Empleo',
                    'body'  => 'Los Tipos de Empleo han sido creados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Tipo de Empleo',
            'code'    => 'Código',
            'country' => 'País',
        ],
    ],
];
