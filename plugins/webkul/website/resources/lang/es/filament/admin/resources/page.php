<?php

return [
    'navigation' => [
        'title' => 'Páginas',
        'group' => 'Sitio Web',
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
                    'meta-title'       => 'Meta Título',
                    'meta-keywords'    => 'Meta Palabras Clave',
                    'meta-description' => 'Meta Descripción',
                ],
            ],

            'settings' => [
                'title' => 'Configuración',

                'fields' => [
                    'is-header-visible' => 'Es Visible en el Menú de Encabezado',
                    'is-footer-visible' => 'Es Visible en el Menú de Pie de Página',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'             => 'Título',
            'slug'              => 'Slug',
            'creator'           => 'Creado Por',
            'is-published'      => 'Está Publicada',
            'is-header-visible' => 'Es Visible en el Menú de Encabezado',
            'is-footer-visible' => 'Es Visible en el Menú de Pie de Página',
            'created-at'        => 'Creado El',
            'updated-at'        => 'Actualizado El',
        ],

        'groups' => [
            'created-at' => 'Creado El',
        ],

        'filters' => [
            'is-published' => 'Está Publicada',
            'creator'      => 'Creado Por',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Página actualizada',
                    'body'  => 'La página ha sido actualizada exitosamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Página restaurada',
                    'body'  => 'La página ha sido restaurada exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Página eliminada',
                    'body'  => 'La página ha sido eliminada exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Página eliminada permanentemente',
                    'body'  => 'La página ha sido eliminada permanentemente exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Páginas restauradas',
                    'body'  => 'Las páginas han sido restauradas exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Páginas eliminadas',
                    'body'  => 'Las páginas han sido eliminadas exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Páginas eliminadas permanentemente',
                    'body'  => 'Las páginas han sido eliminadas permanentemente exitosamente.',
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
                'title' => 'Configuración',

                'entries' => [
                    'is-header-visible' => 'Es Visible en el Menú de Encabezado',
                    'is-footer-visible' => 'Es Visible en el Menú de Pie de Página',
                ],
            ],
        ],
    ],
];
