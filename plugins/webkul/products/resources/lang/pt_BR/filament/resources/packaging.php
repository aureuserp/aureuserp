<?php

return [
    'form' => [
        'name' => 'Nome',
        'barcode' => 'Código de barras',
        'product' => 'Produto',
        'routes' => 'Rotas',
        'qty' => 'Qtd.',
        'company' => 'Empresa',
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'product' => 'Produto',
            'routes' => 'Rotas',
            'qty' => 'Qtd.',
            'company' => 'Empresa',
            'barcode' => 'Código de barras',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'product' => 'Produto',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'product' => 'Produto',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Embalagem atualizada',
                    'body' => 'A embalagem foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Embalagem excluída',
                        'body' => 'A embalagem foi excluída com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Embalagem não pôde ser excluída',
                        'body' => 'A embalagem não pode ser excluída porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Embalagens excluídas',
                        'body' => 'As embalagens foram excluídas com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Embalagens não puderam ser excluídas',
                        'body' => 'As embalagens não podem ser excluídas porque estão em uso no momento.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Nova embalagem',

                'notification' => [
                    'title' => 'Embalagem criada',
                    'body' => 'A embalagem foi criada com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'name' => 'Nome da embalagem',
                    'barcode' => 'Código de barras',
                    'product' => 'Produto',
                    'qty' => 'Quantidade',
                ],
            ],

            'organization' => [
                'title' => 'Detalhes da organização',

                'entries' => [
                    'company' => 'Empresa',
                    'creator' => 'Criado por',
                    'created_at' => 'Criado em',
                    'updated_at' => 'Última atualização em',
                ],
            ],
        ],
    ],
];
