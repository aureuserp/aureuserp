<?php

return [
    'navigation' => [
        'title' => 'Categorias de armazenamento',
        'group' => 'Gestão de armazéns',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                    'allow-new-products' => 'Permitir novos produtos',
                    'max-weight' => 'Peso máximo',
                    'company' => 'Empresa',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'allow-new-products' => 'Permitir novos produtos',
            'max-weight' => 'Peso máximo',
            'company' => 'Empresa',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'allow-new-products' => 'Permitir novos produtos',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categoria de armazenamento excluída',
                    'body' => 'A categoria de armazenamento foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Categorias de armazenamento excluídas',
                    'body' => 'As categorias de armazenamento foram excluídas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'name' => 'Nome',
                    'allow-new-products' => 'Permitir novos produtos',
                    'max-weight' => 'Peso máximo',
                    'company' => 'Empresa',
                ],
            ],

            'record-information' => [
                'title' => 'Informações do registro',

                'entries' => [
                    'created-by' => 'Criado por',
                    'created-at' => 'Criado em',
                    'last-updated' => 'Última atualização',
                ],
            ],
        ],
    ],
];
