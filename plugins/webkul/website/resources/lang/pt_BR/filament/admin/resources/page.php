<?php

return [
    'navigation' => [
        'title' => 'Páginas',
        'group' => 'Site',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'title' => 'Título',
                    'title-placeholder' => 'Título da página ...',
                    'slug' => 'Slug',
                    'content' => 'Conteúdo',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'fields' => [
                    'meta-title' => 'Meta título',
                    'meta-keywords' => 'Meta palavras-chave',
                    'meta-description' => 'Meta descrição',
                ],
            ],

            'settings' => [
                'title' => 'Configurações',

                'fields' => [
                    'is-header-visible' => 'Visível no menu do cabeçalho',
                    'is-footer-visible' => 'Visível no menu do rodapé',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title' => 'Título',
            'slug' => 'Slug',
            'creator' => 'Criado por',
            'is-published' => 'Está publicado',
            'is-header-visible' => 'Visível no menu do cabeçalho',
            'is-footer-visible' => 'Visível no menu do rodapé',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'is-published' => 'Está publicado',
            'creator' => 'Criado por',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Página atualizada',
                    'body' => 'A página foi atualizada com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Página restaurada',
                    'body' => 'A página foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Página excluída',
                    'body' => 'A página foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Página excluída permanentemente',
                    'body' => 'A página foi excluída permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Páginas restauradas',
                    'body' => 'As páginas foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Páginas excluídas',
                    'body' => 'As páginas foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Páginas excluídas permanentemente',
                    'body' => 'As páginas foram excluídas permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'title' => 'Título',
                    'slug' => 'Slug',
                    'content' => 'Conteúdo',
                    'banner' => 'Banner',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'entries' => [
                    'meta-title' => 'Meta título',
                    'meta-keywords' => 'Meta palavras-chave',
                    'meta-description' => 'Meta descrição',
                ],
            ],

            'record-information' => [
                'title' => 'Informações do registro',

                'entries' => [
                    'author' => 'Autor',
                    'created-by' => 'Criado por',
                    'published-at' => 'Publicado em',
                    'last-updated-by' => 'Última atualização por',
                    'last-updated' => 'Última atualização em',
                    'created-at' => 'Criado em',
                ],
            ],

            'settings' => [
                'title' => 'Configurações',

                'entries' => [
                    'is-header-visible' => 'Visível no menu do cabeçalho',
                    'is-footer-visible' => 'Visível no menu do rodapé',
                ],
            ],
        ],
    ],
];
