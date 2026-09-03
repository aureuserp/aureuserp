<?php

return [
    'navigation' => [
        'title' => 'Categorias',
        'group' => 'Blog',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nome',
            'name-placeholder' => 'Título da categoria ...',
            'sub-title'        => 'Subtítulo',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'nome',
            'sub-title'  => 'Subtítulo',
            'posts'      => 'Publicações',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'is-published' => 'Está publicado',
            'author'       => 'Autor',
            'creator'      => 'Criado por',
            'category'     => 'Categoria',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoria atualizada',
                    'body'  => 'A categoria foi atualizada com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Categoria restaurada',
                    'body'  => 'A categoria foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categoria excluída',
                    'body'  => 'A categoria foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categoria excluída permanentemente',
                        'body'  => 'A categoria foi excluída permanentemente com sucesso.',
                    ],
                    'error' => [
                        'title' => 'Não foi possível excluir a categoria',
                        'body'  => 'A categoria não pode ser excluída porque está em uso no momento.',
                    ],
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'Não é possível excluir a categoria',
                    'body'  => 'Você não pode excluir esta categoria porque ela está associada a alguns posts.',
                ],

                'exception' => 'Você não pode excluir permanentemente esta categoria porque ela está associada a alguns posts.',
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Categorias restauradas',
                    'body'  => 'As categorias foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categorias excluídas',
                    'body'  => 'As categorias foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Categorias excluídas permanentemente',
                    'body'  => 'As categorias foram excluídas permanentemente com sucesso.',
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'Não é possível excluir a categoria',
                    'body'  => 'Você não pode excluir esta categoria porque ela está associada a alguns posts.',
                ],
            ],
        ],
    ],

    'infolist' => [
    ],
];
