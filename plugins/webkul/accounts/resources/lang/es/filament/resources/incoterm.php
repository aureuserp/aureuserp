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
            'created-by' => 'Creado por',
        ],

        'groups' => [
            'code' => 'Código',
            'name' => 'Nombre',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm actualizado',
                    'body'  => 'El incoterm se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm eliminado',
                    'body'  => 'El incoterm se ha eliminado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm restaurado',
                    'body'  => 'El incoterm se ha restaurado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterms restaurados',
                    'body'  => 'Los incoterms se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterms eliminados',
                    'body'  => 'Los incoterms se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms eliminados permanentemente',
                    'body'  => 'Los incoterms se han eliminado permanentemente correctamente.',
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
