<?php

return [
    'navigation' => [
        'title' => 'Produtos',
        'group' => 'Estoque',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name'             => 'Nome',
                    'name-placeholder' => 'ex.: camiseta',
                    'description'      => 'Descrição',
                    'tags'             => 'Tags',
                    'sales'            => 'Vendas',
                    'purchase'         => 'Compras',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Política de faturamento',
                'ordered-policy'   => 'Você pode faturar produtos antes que sejam entregues.',
                'delivered-policy' => 'Faturar após a entrega, com base nas quantidades entregues, não nas pedidas.',
            ],

            'images' => [
                'title' => 'Imagens',
            ],

            'settings' => [
                'title' => 'Configurações',

                'fields' => [
                    'type'      => 'Tipo',
                    'reference' => 'Referência',
                    'barcode'   => 'Código de barras',
                    'category'  => 'Categoria',
                    'company'   => 'Empresa',
                ],
            ],

            'category-and-tags' => [
                'title' => 'Categoria e tags',

                'fields' => [
                    'category' => 'Categoria',
                    'tags'     => 'Tags',
                ],
            ],

            'pricing' => [
                'title' => 'Precificação',

                'fields' => [
                    'price' => 'Preço',
                    'cost'  => 'Custo',
                ],
            ],

            'additional' => [
                'title' => 'Adicional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nome',
            'images'      => 'Imagens',
            'type'        => 'Tipo',
            'reference'   => 'Referência',
            'responsible' => 'Responsável',
            'barcode'     => 'Código de barras',
            'category'    => 'Categoria',
            'company'     => 'Empresa',
            'price'       => 'Preço',
            'cost'        => 'Custo',
            'tags'        => 'Tags',
            'deleted-at'  => 'Excluído em',
            'created-at'  => 'Criado em',
            'updated-at'  => 'Atualizado em',
        ],

        'groups' => [
            'type'       => 'Tipo',
            'category'   => 'Categoria',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'name'        => 'Nome',
            'type'        => 'Tipo',
            'reference'   => 'Referência',
            'barcode'     => 'Código de barras',
            'category'    => 'Categoria',
            'company'     => 'Empresa',
            'price'       => 'Preço',
            'cost'        => 'Custo',
            'is-favorite' => 'É favorito',
            'weight'      => 'Peso',
            'volume'      => 'Volume',
            'tags'        => 'Tags',
            'responsible' => 'Responsável',
            'created-at'  => 'Criado em',
            'updated-at'  => 'Atualizado em',
            'creator'     => 'Criador',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Produto restaurado',
                    'body'  => 'O produto foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produto excluído',
                    'body'  => 'O produto foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Produto excluído permanentemente',
                    'body'  => 'O produto foi excluído permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Produtos restaurados',
                    'body'  => 'Os produtos foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produtos excluídos',
                    'body'  => 'Os produtos foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Produtos excluídos permanentemente',
                    'body'  => 'Os produtos foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'name'             => 'Nome',
                    'name-placeholder' => 'ex.: camiseta',
                    'description'      => 'Descrição',
                    'tags'             => 'Tags',
                ],
            ],

            'images' => [
                'title' => 'Imagens',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Configurações',

                'entries' => [
                    'type'      => 'Tipo',
                    'reference' => 'Referência',
                    'barcode'   => 'Código de barras',
                    'category'  => 'Categoria',
                    'company'   => 'Empresa',
                ],
            ],

            'pricing' => [
                'title' => 'Precificação',

                'entries' => [
                    'price' => 'Preço',
                    'cost'  => 'Custo',
                ],
            ],

            'inventory' => [
                'title' => 'Estoque',

                'entries' => [],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Rastreamento',

                        'entries' => [
                            'track-inventory' => 'Rastrear estoque',
                            'track-by'        => 'Rastrear por',
                            'expiration-date' => 'Data de validade',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operações',

                        'entries' => [
                            'routes' => 'Rotas',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logística',

                        'entries' => [
                            'responsible' => 'Responsável',
                            'weight'      => 'Peso',
                            'volume'      => 'Volume',
                            'sale-delay'  => 'Prazo de entrega ao cliente (dias)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Rastreabilidade',

                        'entries' => [
                            'expiration-date'  => 'Data de validade (dias)',
                            'best-before-date' => 'Data de consumo preferencial (dias)',
                            'removal-date'     => 'Data de remoção (dias)',
                            'alert-date'       => 'Data de alerta (dias)',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'Informações do registro',

                'entries' => [
                    'created-at' => 'Criado em',
                    'created-by' => 'Criado por',
                    'updated-at' => 'Atualizado em',
                ],
            ],
        ],
    ],
];
