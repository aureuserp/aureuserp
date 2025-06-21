<?php

return [
    'form' => [
        'name'          => 'Nombre',
        'level'         => 'Nivel',
        'default-level' => 'Nivel Predeterminado',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Nombre',
            'level'         => 'Nivel',
            'default-level' => 'Nivel Predeterminado',
            'created-at'    => 'Creado El',
            'updated-at'    => 'Actualizado El',
        ],

        'groups' => [
            'created-at' => 'Creado El',
        ],

        'filters' => [
            'deleted-records' => 'Registros Eliminados',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Nivel de Habilidad creado',
                    'body'  => 'El nivel de habilidad ha sido creado exitosamente.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'Nivel de Habilidad actualizado',
                    'body'  => 'El nivel de habilidad ha sido actualizado exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Nivel de Habilidad restaurado',
                    'body'  => 'El nivel de habilidad ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nivel de Habilidad eliminado',
                    'body'  => 'El nivel de habilidad ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Niveles de Habilidad eliminados',
                    'body'  => 'Los niveles de habilidad han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Niveles de Habilidad eliminados permanentemente',
                    'body'  => 'Los niveles de habilidad han sido eliminados permanentemente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Niveles de Habilidad restaurados',
                    'body'  => 'Los niveles de habilidad han sido restaurados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'          => 'Nombre',
            'level'         => 'Nivel',
            'default-level' => 'Nivel Predeterminado',
        ],
    ],
];
