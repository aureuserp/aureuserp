<?php

return [
    'navigation' => [
        'title' => 'Posts do blog',
        'group' => 'Site',
    ],

    'global-search' => [
        'author' => 'Autor',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'title' => 'Título',
                    'sub-title' => 'Subtítulo',
                    'title-placeholder' => 'Título do post ...',
                    'slug' => 'Slug',
                    'content' => 'Conteúdo',
                    'banner' => 'Banner',
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
                    'category' => 'Categoria',
                    'tags' => 'Tags',
                    'name' => 'Nome',
                    'color' => 'Cor',
                    'is-published' => 'Está publicado',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title' => 'Título',
            'slug' => 'Slug',
            'author' => 'Autor',
            'category' => 'Categoria',
            'creator' => 'Criado por',
            'is-published' => 'Está publicado',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'category' => 'Categoria',
            'author' => 'Autor',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'is-published' => 'Está publicado',
            'author' => 'Autor',
            'creator' => 'Criado por',
            'category' => 'Categoria',
            'tags' => 'Tags',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Post atualizado',
                    'body' => 'O post foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Post restaurado',
                    'body' => 'O post foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Post excluído',
                    'body' => 'O post foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Post excluído permanentemente',
                    'body' => 'O post foi excluído permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Posts restaurados',
                    'body' => 'Os posts foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Posts excluídos',
                    'body' => 'Os posts foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Posts excluídos permanentemente',
                    'body' => 'Os posts foram excluídos permanentemente com sucesso.',
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
                    'category' => 'Categoria',
                    'tags' => 'Tags',
                    'name' => 'Nome',
                    'color' => 'Cor',
                    'is-published' => 'Está publicado',
                ],
            ],
        ],
    ],
];
