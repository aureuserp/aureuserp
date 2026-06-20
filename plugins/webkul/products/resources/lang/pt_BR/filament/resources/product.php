<?php

return [
    'global-search' => [
        'reference' => 'Referência',
        'barcode' => 'Código de barras',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'name' => 'Nome',
                    'name-placeholder' => 'ex.: camiseta',
                    'description' => 'Descrição',
                    'tags' => 'Tags',
                ],
            ],

            'images' => [
                'title' => 'Imagens',
            ],

            'inventory' => [
                'title' => 'Estoque',

                'fields' => [],

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logística',

                        'fields' => [
                            'weight' => 'Peso',
                            'volume' => 'Volume',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Configurações',

                'fields' => [
                    'type' => 'Tipo',
                    'reference' => 'Referência',
                    'barcode' => 'Código de barras',
                    'category' => 'Categoria',
                    'company' => 'Empresa',
                ],
            ],

            'pricing' => [
                'title' => 'Precificação',

                'fields' => [
                    'price' => 'Preço',
                    'cost' => 'Custo',
                    'uom-placeholder' => 'Unidade de medida',
                ],
            ],

            'additional' => [
                'title' => 'Adicional',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite' => 'Favorito',
            'name' => 'Nome',
            'variants' => 'Variantes',
            'images' => 'Imagens',
            'type' => 'Tipo',
            'reference' => 'Referência',
            'responsible' => 'Responsável',
            'barcode' => 'Código de barras',
            'category' => 'Categoria',
            'company' => 'Empresa',
            'price' => 'Preço',
            'cost' => 'Custo',
            'on-hand' => 'Em mãos',
            'tags' => 'Tags',
            'deleted-at' => 'Excluído em',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'type' => 'Tipo',
            'category' => 'Categoria',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'name' => 'Nome',
            'type' => 'Tipo',
            'reference' => 'Referência',
            'barcode' => 'Código de barras',
            'category' => 'Categoria',
            'company' => 'Empresa',
            'price' => 'Preço',
            'cost' => 'Custo',
            'is-favorite' => 'É favorito',
            'weight' => 'Peso',
            'volume' => 'Volume',
            'tags' => 'Tags',
            'responsible' => 'Responsável',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
            'creator' => 'Criador',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Produto restaurado',
                    'body' => 'O produto foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produto excluído',
                    'body' => 'O produto foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Produto excluído permanentemente',
                        'body' => 'O produto foi excluído permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Produto não pôde ser excluído',
                        'body' => 'O produto não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Imprimir etiquetas',

                'form' => [
                    'fields' => [
                        'quantity' => 'Número de etiquetas',
                        'format' => 'Formato',

                        'format-options' => [
                            'dymo' => 'Dymo',
                            '2x7_price' => '2x7 com preço',
                            '4x7_price' => '4x7 com preço',
                            '4x12' => '4x12',
                            '4x12_price' => '4x12 com preço',
                        ],
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Produtos restaurados',
                    'body' => 'Os produtos foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produtos excluídos',
                    'body' => 'Os produtos foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Produtos excluídos permanentemente',
                        'body' => 'Os produtos foram excluídos permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Produtos não puderam ser excluídos',
                        'body' => 'Os produtos não podem ser excluídos porque estão em uso no momento.',
                    ],
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
                    'name-placeholder' => 'ex.: camiseta',
                    'description' => 'Descrição',
                    'tags' => 'Tags',
                ],
            ],

            'images' => [
                'title' => 'Imagens',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Configurações',

                'entries' => [
                    'type' => 'Tipo',
                    'reference' => 'Referência',
                    'barcode' => 'Código de barras',
                    'category' => 'Categoria',
                    'company' => 'Empresa',
                ],
            ],

            'pricing' => [
                'title' => 'Precificação',

                'entries' => [
                    'price' => 'Preço',
                    'cost' => 'Custo',
                ],
            ],

            'inventory' => [
                'title' => 'Estoque',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logística',

                        'entries' => [
                            'weight' => 'Peso',
                            'volume' => 'Volume',
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
