<?php

return [
    'navigation' => [
        'title' => 'Categorías',
        'group' => 'Blog',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nombre',
            'name-placeholder' => 'Título de la categoría ...',
            'sub-title'        => 'Subtítulo',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'sub-title'  => 'Subtítulo',
            'posts'      => 'Publicaciones',
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'is-published' => 'Está publicado',
            'author'       => 'Autor',
            'creator'      => 'Creado por',
            'category'     => 'Categoría',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoría actualizada',
                    'body'  => 'La categoría se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Categoría restaurada',
                    'body'  => 'La categoría se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categoría eliminada',
                    'body'  => 'La categoría se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categoría eliminada definitivamente',
                        'body'  => 'La categoría se ha eliminado definitivamente correctamente.',
                    ],
                    'error' => [
                        'title' => 'No se pudo eliminar la categoría',
                        'body'  => 'La categoría no se puede eliminar porque está actualmente en uso.',
                    ],
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'No se puede eliminar la categoría',
                    'body'  => 'No puede eliminar esta categoría porque está asociada a algunas publicaciones.',
                ],

                'exception' => 'No puede eliminar definitivamente esta categoría porque está asociada a algunas publicaciones.',
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Categorías restauradas',
                    'body'  => 'Las categorías se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categorías eliminadas',
                    'body'  => 'Las categorías se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Categorías eliminadas definitivamente',
                    'body'  => 'Las categorías se han eliminado definitivamente correctamente.',
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'No se puede eliminar la categoría',
                    'body'  => 'No puede eliminar esta categoría porque está asociada a algunas publicaciones.',
                ],
            ],
        ],
    ],

    'infolist' => [
    ],
];
