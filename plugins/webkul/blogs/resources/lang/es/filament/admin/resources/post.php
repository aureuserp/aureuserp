<?php

return [
    'navigation' => [
        'title' => 'Entradas de Blog',
        'group' => 'Sitio Web',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'title'             => 'Título',
                    'sub-title'         => 'Subtítulo',
                    'title-placeholder' => 'Título de la entrada ...',
                    'slug'              => 'Slug',
                    'content'           => 'Contenido',
                    'banner'            => 'Banner',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'fields' => [
                    'meta-title'       => 'Meta Título',
                    'meta-keywords'    => 'Meta Palabras Clave',
                    'meta-description' => 'Meta Descripción',
                ],
            ],

            'settings' => [
                'title' => 'Configuraciones',

                'fields' => [
                    'category'     => 'Categoría',
                    'tags'         => 'Etiquetas',
                    'name'         => 'Nombre',
                    'color'        => 'Color',
                    'is-published' => 'Está Publicado',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Título',
            'slug'         => 'Slug',
            'author'       => 'Autor',
            'category'     => 'Categoría',
            'creator'      => 'Creado Por',
            'is-published' => 'Está Publicado',
            'created-at'   => 'Creado El',
            'updated-at'   => 'Actualizado El',
        ],

        'groups' => [
            'category'   => 'Categoría',
            'author'     => 'Autor',
            'created-at' => 'Creado El',
        ],

        'filters' => [
            'is-published' => 'Está Publicado',
            'author'       => 'Autor',
            'creator'      => 'Creado Por',
            'category'     => 'Categoría',
            'tags'         => 'Etiquetas',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Entrada actualizada',
                    'body'  => 'La entrada ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Entrada restaurada',
                    'body'  => 'La entrada ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Entrada eliminada',
                    'body'  => 'La entrada ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Entrada eliminada permanentemente',
                    'body'  => 'La entrada ha sido eliminada permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Entradas restauradas',
                    'body'  => 'Las entradas han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Entradas eliminadas',
                    'body'  => 'Las entradas han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Entradas eliminadas permanentemente',
                    'body'  => 'Las entradas han sido eliminadas permanentemente exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'title'   => 'Título',
                    'slug'    => 'Slug',
                    'content' => 'Contenido',
                    'banner'  => 'Banner',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'entries' => [
                    'meta-title'       => 'Meta Título',
                    'meta-keywords'    => 'Meta Palabras Clave',
                    'meta-description' => 'Meta Descripción',
                ],
            ],

            'record-information' => [
                'title' => 'Información del Registro',

                'entries' => [
                    'author'          => 'Autor',
                    'created-by'      => 'Creado Por',
                    'published-at'    => 'Publicado El',
                    'last-updated-by' => 'Última Actualización Por',
                    'last-updated'    => 'Última Actualización El',
                    'created-at'      => 'Creado El',
                ],
            ],

            'settings' => [
                'title' => 'Configuraciones',

                'entries' => [
                    'category'     => 'Categoría',
                    'tags'         => 'Etiquetas',
                    'name'         => 'Nombre',
                    'color'        => 'Color',
                    'is-published' => 'Está Publicado',
                ],
            ],
        ],
    ],
];
