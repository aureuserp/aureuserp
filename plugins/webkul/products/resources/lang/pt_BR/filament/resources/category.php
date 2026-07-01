<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'ex.: luminárias',
                    'parent' => 'Pai',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'full-name' => 'Nome completo',
            'parent-path' => 'Caminho pai',
            'parent' => 'Pai',
            'creator' => 'Criador',
            'created-at' => 'Criado em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'parent' => 'Pai',
            'creator' => 'Criador',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'parent' => 'Pai',
            'creator' => 'Criador',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categoria excluída',
                        'body' => 'A categoria foi excluída com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Categoria não pôde ser excluída',
                        'body' => 'A categoria não pode ser excluída porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Categorias excluídas',
                        'body' => 'As categorias foram excluídas com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Categorias não puderam ser excluídas',
                        'body' => 'As categorias não podem ser excluídas porque estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'name' => 'Nome',
                    'parent' => 'Categoria pai',
                    'full_name' => 'Nome completo da categoria',
                    'parent_path' => 'Caminho da categoria',
                ],
            ],

            'record-information' => [
                'title' => 'Informações do registro',

                'entries' => [
                    'creator' => 'Criado por',
                    'created_at' => 'Criado em',
                    'updated_at' => 'Última atualização em',
                ],
            ],
        ],
    ],
];
