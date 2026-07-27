<?php

return [
    'form' => [
        'name'          => 'Nombre',
        'level'         => 'Nivel',
        'default-level' => 'Nivel predeterminado',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Nombre',
            'level'         => 'Nivel',
            'default-level' => 'Nivel predeterminado',
            'created-at'    => 'Creado el',
            'updated-at'    => 'Actualizado el',
        ],

        'groups' => [
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'deleted-records' => 'Registros eliminados',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Nivel de competencia creado',
                    'body'  => 'El nivel de competencia se ha creado correctamente.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'Nivel de competencia actualizado',
                    'body'  => 'El nivel de competencia se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Nivel de competencia restaurado',
                    'body'  => 'El nivel de competencia se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nivel de competencia eliminado',
                    'body'  => 'El nivel de competencia se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Niveles de competencia eliminados',
                    'body'  => 'Las competencias se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Niveles de competencia eliminados permanentemente',
                    'body'  => 'Las competencias se han eliminado permanentemente correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Niveles de competencia restaurados',
                    'body'  => 'Las competencias se han restaurado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'          => 'Nombre',
            'level'         => 'Nivel',
            'default-level' => 'Nivel predeterminado',
        ],
    ],
];
