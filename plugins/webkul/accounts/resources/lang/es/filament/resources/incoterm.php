<?php

return [
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
                    'body'  => 'El Incoterm ha sido actualizado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm eliminado',
                    'body'  => 'El Incoterm ha sido eliminado exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm restaurado',
                    'body'  => 'El Incoterm ha sido restaurado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterms restaurados',
                    'body'  => 'Los Incoterms han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterms eliminados',
                    'body'  => 'Los Incoterms han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms eliminados permanentemente',
                    'body'  => 'Los Incoterms han sido eliminados permanentemente exitosamente.',
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
