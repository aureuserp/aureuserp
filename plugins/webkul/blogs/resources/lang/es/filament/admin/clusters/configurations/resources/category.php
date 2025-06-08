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
            'posts'      => 'Entradas',
            'created-at' => 'Creado El',
        ],

        'filters' => [
            'is-published' => 'Está Publicado',
            'author'       => 'Autor',
            'creator'      => 'Creado Por',
            'category'     => 'Categoría',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoría actualizada',
                    'body'  => 'La categoría ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Categoría restaurada',
                    'body'  => 'La categoría ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categoría eliminada',
                    'body'  => 'La categoría ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Categoría eliminada permanentemente',
                    'body'  => 'La categoría ha sido eliminada permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Categorías restauradas',
                    'body'  => 'Las categorías han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categorías eliminadas',
                    'body'  => 'Las categorías han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Categorías eliminadas permanentemente',
                    'body'  => 'Las categorías han sido eliminadas permanentemente exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
    ],
];
