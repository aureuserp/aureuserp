<?php

return [
    'navigation' => [
        'title' => 'Etapas',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nombre',
            'done' => 'Hecho',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'done'       => 'Hecho',
            'created-at' => 'Creado el',
        ],

        'groups' => [
            'done'       => 'Hecho',
            'created-at' => 'Creado el',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etapa actualizada',
                    'body'  => 'La etapa se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapa eliminada',
                    'body'  => 'La etapa se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etapas eliminadas',
                    'body'  => 'Las etapas se han eliminado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'entries' => [
                    'name' => 'Nombre',
                    'done' => 'Hecho',
                ],
            ],
        ],
    ],
];
