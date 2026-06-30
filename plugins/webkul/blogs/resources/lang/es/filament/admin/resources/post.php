<?php

return [
    'navigation' => [
        'title' => 'Publicaciones del blog',
        'group' => 'Sitio web',
    ],

    'global-search' => [
        'author' => 'Autor',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'title'             => 'Título',
                    'sub-title'         => 'Subtítulo',
                    'title-placeholder' => 'Título de la publicación ...',
                    'slug'              => 'Slug',
                    'content'           => 'Contenido',
                    'banner'            => 'Banner',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'fields' => [
                    'meta-title'       => 'Meta título',
                    'meta-keywords'    => 'Meta palabras clave',
                    'meta-description' => 'Meta descripción',
                ],
            ],

            'settings' => [
                'title' => 'Configuración',

                'fields' => [
                    'category'     => 'Categoría',
                    'tags'         => 'Etiquetas',
                    'name'         => 'Nombre',
                    'color'        => 'Color',
                    'is-published' => 'Publicado',
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
            'creator'      => 'Creado por',
            'is-published' => 'Publicado',
            'created-at'   => 'Creado el',
            'updated-at'   => 'Actualizado el',
        ],

        'groups' => [
            'category'   => 'Categoría',
            'author'     => 'Autor',
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'is-published' => 'Publicado',
            'author'       => 'Autor',
            'creator'      => 'Creado por',
            'category'     => 'Categoría',
            'tags'         => 'Etiquetas',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Publicación actualizada',
                    'body'  => 'La publicación se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Publicación restaurada',
                    'body'  => 'La publicación se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Publicación eliminada',
                    'body'  => 'La publicación se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Publicación eliminada permanentemente',
                    'body'  => 'La publicación se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Publicaciones restauradas',
                    'body'  => 'Las publicaciones se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Publicaciones eliminadas',
                    'body'  => 'Las publicaciones se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Publicaciones eliminadas permanentemente',
                    'body'  => 'Las publicaciones se han eliminado permanentemente correctamente.',
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
                    'meta-title'       => 'Meta título',
                    'meta-keywords'    => 'Meta palabras clave',
                    'meta-description' => 'Meta descripción',
                ],
            ],

            'record-information' => [
                'title' => 'Información del registro',

                'entries' => [
                    'author'          => 'Autor',
                    'created-by'      => 'Creado por',
                    'published-at'    => 'Publicado el',
                    'last-updated-by' => 'Última actualización por',
                    'last-updated'    => 'Última actualización el',
                    'created-at'      => 'Creado el',
                ],
            ],

            'settings' => [
                'title' => 'Configuración',

                'entries' => [
                    'category'     => 'Categoría',
                    'tags'         => 'Etiquetas',
                    'name'         => 'Nombre',
                    'color'        => 'Color',
                    'is-published' => 'Publicado',
                ],
            ],
        ],
    ],
];
