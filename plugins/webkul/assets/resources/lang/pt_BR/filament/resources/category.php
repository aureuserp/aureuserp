<?php

return [
    'title'        => 'Categoria de ativo',
    'plural-title' => 'Categorias de ativos',

    'navigation' => [
        'title' => 'Categorias de ativos',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Detalhes da categoria',
                'fields' => [
                    'name'   => 'Nome da categoria',
                    'parent' => 'Categoria pai',
                ],
            ],

            'settings' => [
                'title'  => 'Configurações',
                'fields' => [
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nome',
            'full-name'      => 'Caminho completo',
            'parent'         => 'Categoria pai',
            'products-count' => 'Produtos',
            'assets-count'   => 'Ativos',
            'total-cost'     => 'Custo total',
            'company'        => 'Empresa',
            'creator'        => 'Criado por',
            'created-at'     => 'Criado em',
            'updated-at'     => 'Atualizado em',
        ],

        'filters' => [
            'parent'  => 'Categoria pai',
            'company' => 'Empresa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Categoria atualizada',
                    'body'  => 'A categoria foi atualizada com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Detalhes da categoria',
                'entries' => [
                    'name'           => 'Nome',
                    'full-name'      => 'Caminho completo',
                    'parent'         => 'Categoria pai',
                    'products-count' => 'Produtos',
                    'assets-count'   => 'Ativos',
                    'total-cost'     => 'Custo total',
                ],
            ],

            'audit' => [
                'title'   => 'Auditoria',
                'entries' => [
                    'creator'    => 'Criado por',
                    'created-at' => 'Criado em',
                    'updated-at' => 'Atualizado em',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'category-products' => [
            'title' => 'Produtos desta categoria',
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'Nova categoria',
                    'notification' => [
                        'title' => 'Categoria criada',
                        'body'  => 'A categoria foi criada com sucesso.',
                    ],
                ],
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Categoria criada',
                'body'  => 'A categoria foi criada com sucesso.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Categoria atualizada',
                'body'  => 'A categoria foi atualizada com sucesso.',
            ],
        ],
    ],
];
