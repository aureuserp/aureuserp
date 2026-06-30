<?php

return [
    'title' => 'Tipos de empleo',

    'navigation' => [
        'title' => 'Tipos de empleo',
        'group' => 'Reclutamiento',
    ],

    'form' => [
        'fields' => [
            'name'    => 'Tipo de empleo',
            'code'    => 'Código',
            'country' => 'País',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Tipo de empleo',
            'code'       => 'Código',
            'country'    => 'País',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'name'       => 'Tipo de empleo',
            'country'    => 'País',
            'created-by' => 'Creado por',
            'updated-at' => 'Actualizado el',
            'created-at' => 'Creado el',
        ],

        'groups' => [
            'name'       => 'Tipo de empleo',
            'country'    => 'País',
            'code'       => 'Código',
            'created-by' => 'Creado por',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tipo de empleo',
                    'body'  => 'El tipo de empleo se ha editado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de empleo eliminado',
                    'body'  => 'El tipo de empleo se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipos de empleo eliminados',
                    'body'  => 'Los tipos de empleo se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tipos de empleo',
                    'body'  => 'Los tipos de empleo se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Tipo de empleo',
            'code'    => 'Código',
            'country' => 'País',
        ],
    ],
];
