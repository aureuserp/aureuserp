<?php

return [
    'title' => 'Produtos do modelo de pedido',

    'navigation' => [
        'title' => 'Produtos do modelo de pedido',
        'group' => 'Pedidos de venda',
    ],

    'global-search' => [
        'name' => 'Nome',
    ],

    'form' => [
        'fields' => [
            'sort' => 'Ordenar',
            'order-template' => 'Modelo de pedido',
            'company' => 'Empresa',
            'product' => 'Produto',
            'product-uom' => 'Unidade de medida do produto',
            'creator' => 'Criador',
            'display-type' => 'Tipo de exibição',
            'name' => 'Nome',
            'quantity' => 'Quantidade',
        ],
    ],

    'table' => [
        'columns' => [
            'sort' => 'Ordenar',
            'order-template' => 'Modelo de pedido',
            'company' => 'Empresa',
            'product' => 'Produto',
            'product-uom' => 'Unidade de medida do produto',
            'created-by' => 'Criado por',
            'display-type' => 'Tipo de exibição',
            'name' => 'Nome',
            'quantity' => 'Quantidade',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',

        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Produtos do modelo de pedido atualizados',
                    'body' => 'Os produtos do modelo de pedido foram atualizados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produtos do modelo de pedido excluídos',
                    'body' => 'Os produtos do modelo de pedido foram excluídos com sucesso.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Produtos do modelo de pedido excluídos',
                    'body' => 'Os produtos do modelo de pedido foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort' => 'Ordem de classificação',
            'order-template' => 'Modelo de pedido',
            'company' => 'Empresa',
            'product' => 'Produto',
            'product-uom' => 'Unidade de medida do produto',
            'display-type' => 'Tipo de exibição',
            'name' => 'Nome',
            'quantity' => 'Quantidade',
        ],
    ],
];
