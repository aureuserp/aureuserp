<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',

                'fields' => [
                    'name'             => 'Nombre',
                    'name-placeholder' => 'ej. Lámparas',
                    'parent'           => 'Superior',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nombre',
            'full-name'   => 'Nombre completo',
            'parent-path' => 'Ruta superior',
            'parent'      => 'Superior',
            'creator'     => 'Creador',
            'created-at'  => 'Creado el',
            'created-at'  => 'Creado el',
            'updated-at'  => 'Actualizado el',
        ],

        'groups' => [
            'parent'     => 'Superior',
            'creator'    => 'Creador',
            'created-at' => 'Creado el',
            'updated-at' => 'Actualizado el',
        ],

        'filters' => [
            'parent'  => 'Superior',
            'creator' => 'Creador',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categoría eliminada',
                        'body'  => 'La categoría ha sido eliminada correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudo eliminar la categoría',
                        'body'  => 'La categoría no puede eliminarse porque está en uso.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categorías eliminadas',
                        'body'  => 'Las categorías han sido eliminadas correctamente.',
                    ],

                    'error' => [
                        'title' => 'No se pudieron eliminar las categorías',
                        'body'  => 'Las categorías no pueden eliminarse porque están en uso.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Información general',

                'entries' => [
                    'name'        => 'Nombre',
                    'parent'      => 'Categoría superior',
                    'full_name'   => 'Nombre completo de la categoría',
                    'parent_path' => 'Ruta de la categoría',
                ],
            ],

            'record-information' => [
                'title' => 'Información del registro',

                'entries' => [
                    'creator'    => 'Creado por',
                    'created_at' => 'Creado el',
                    'updated_at' => 'Última actualización',
                ],
            ],
        ],
    ],
];
