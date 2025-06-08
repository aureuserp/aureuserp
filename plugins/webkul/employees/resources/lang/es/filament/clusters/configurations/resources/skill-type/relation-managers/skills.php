<?php

return [
    'form' => [
        'name' => 'Nombre',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'groups' => [
            'created-at' => 'Creado El',
        ],

        'filters' => [
            'deleted-records' => 'Registros Eliminados',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Habilidad actualizada',
                    'body'  => 'La habilidad ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Habilidad restaurada',
                    'body'  => 'La habilidad ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Habilidad eliminada',
                    'body'  => 'La habilidad ha sido eliminada exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Habilidades eliminadas',
                    'body'  => 'Las habilidades han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Habilidades eliminadas permanentemente',
                    'body'  => 'Las habilidades han sido eliminadas permanentemente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Habilidades restauradas',
                    'body'  => 'Las habilidades han sido restauradas exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nombre',
        ],
    ],
];
