<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Nombre',
                    'name-placeholder' => 'ej. Lámparas',
                    'parent'           => 'Padre',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nombre',
            'full-name'   => 'Nombre Completo',
            'parent-path' => 'Ruta del Padre',
            'parent'      => 'Padre',
            'creator'     => 'Creador',
            'created-at'  => 'Creado El',
            'updated-at'  => 'Actualizado El',
        ],

        'groups' => [
            'parent'     => 'Padre',
            'creator'    => 'Creador',
            'created-at' => 'Creado El',
            'updated-at' => 'Actualizado El',
        ],

        'filters' => [
            'parent'  => 'Padre',
            'creator' => 'Creador',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categoría eliminada',
                        'body'  => 'La categoría ha sido eliminada exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la categoría',
                        'body'  => 'La categoría no puede ser eliminada porque está actualmente en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categorías eliminadas',
                        'body'  => 'Las categorías han sido eliminadas exitosamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las categorías',
                        'body'  => 'Las categorías no pueden ser eliminadas porque están actualmente en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información General',

                'entries' => [
                    'name'        => 'Nombre',
                    'parent'      => 'Categoría Padre',
                    'full_name'   => 'Nombre Completo de la Categoría',
                    'parent_path' => 'Ruta de la Categoría',
                ],
            ],

            'record-information' => [
                'title' => 'Información del Registro',

                'entries' => [
                    'creator'    => 'Creado Por',
                    'created_at' => 'Creado El',
                    'updated_at' => 'Última Actualización El',
                ],
            ],
        ],
    ],
];
