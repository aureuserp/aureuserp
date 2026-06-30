<?php

return [
    'navigation' => [
        'title' => 'Páginas',
        'group' => 'Sitio web',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'title'             => 'Título',
                    'title-placeholder' => 'Título de la página ...',
                    'slug'              => 'Slug',
                    'content'           => 'Contenido',
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
                    'is-header-visible' => 'Mostrar menú de encabezado',
                    'is-footer-visible' => 'Mostrar menú de pie de página',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'             => 'Título',
            'slug'              => 'Slug',
            'creator'           => 'Creado por',
            'is-published'      => 'Publicado',
            'is-header-visible' => 'Mostrar menú de encabezado',
            'is-footer-visible' => 'Mostrar menú de pie de página',
            'created-at'        => 'Creado el',
            'updated-at'        => 'Actualizado el',
        ],

        'groups' => [
            'created-at' => 'Creado el',
        ],

        'filters' => [
            'is-published' => 'Publicado',
            'creator'      => 'Creado por',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Página actualizada',
                    'body'  => 'La página se ha actualizado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Página restaurada',
                    'body'  => 'La página se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Página eliminada',
                    'body'  => 'La página se ha eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Página eliminada permanentemente',
                    'body'  => 'La página se ha eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Páginas restauradas',
                    'body'  => 'Las páginas se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Páginas eliminadas',
                    'body'  => 'Las páginas se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Páginas eliminadas permanentemente',
                    'body'  => 'Las páginas se han eliminado permanentemente correctamente.',
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
                    'is-header-visible' => 'Mostrar menú de encabezado',
                    'is-footer-visible' => 'Mostrar menú de pie de página',
                ],
            ],
        ],
    ],
];
