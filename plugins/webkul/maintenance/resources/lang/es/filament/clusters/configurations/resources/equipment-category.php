<?php

return [
    'navigation' => [
        'title' => 'Categorías',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'fields' => [
                    'name'       => 'Nombre',
                    'technician' => 'Responsable',
                    'company'    => 'Empresa',
                    'note'       => 'Nota',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'technician' => 'Responsable',
            'company'    => 'Empresa',
            'created-at' => 'Creado el',
        ],

        'groups' => [
            'technician' => 'Responsable',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoría actualizada',
                    'body'  => 'La categoría se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categoría eliminada',
                    'body'  => 'La categoría se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorías eliminadas',
                    'body'  => 'Las categorías se han eliminado correctamente.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Categoría creada',
                    'body'  => 'La categoría se ha creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'entries' => [
                    'name'       => 'Nombre',
                    'technician' => 'Responsable',
                    'company'    => 'Empresa',
                    'note'       => 'Nota',
                ],
            ],
        ],
    ],
];
