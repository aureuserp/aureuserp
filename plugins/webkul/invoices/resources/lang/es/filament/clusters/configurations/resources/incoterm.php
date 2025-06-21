<?php

return [
    'title' => 'Incoterms',

    'navigation' => [
        'title' => 'Incoterms',
        'group' => 'Facturación',
    ],

    'global-search' => [
        'name' => 'Nombre',
        'code' => 'Código',
    ],

    'form' => [
        'fields' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Código',
            'name'       => 'Nombre',
            'created-by' => 'Creado Por',
        ],

        'groups' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm actualizado',
                    'body'  => 'El incoterm ha sido actualizado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm eliminado',
                    'body'  => 'El incoterm ha sido eliminado exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm restaurado',
                    'body'  => 'El incoterm ha sido restaurado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterms restaurados',
                    'body'  => 'Los incoterms han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterms eliminados',
                    'body'  => 'Los incoterms han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms eliminados permanentemente',
                    'body'  => 'Los incoterms han sido eliminados permanentemente exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nombre',
            'code' => 'Código',
        ],
    ],
];
