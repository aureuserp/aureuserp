<?php

return [
    'navigation' => [
        'title' => 'Reabastecimento',
        'group' => 'Aquisição',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product' => 'Produto',
            'location' => 'Localização',
            'route' => 'Rota',
            'vendor' => 'Fornecedor',
            'trigger' => 'Disparar',
            'on-hand' => 'Em mãos',
            'min' => 'Mín.',
            'max' => 'Máx.',
            'multiple-quantity' => 'Quantidade múltipla',
            'to-order' => 'A pedir',
            'uom' => 'Unidade de medida',
            'company' => 'Empresa',
        ],

        'groups' => [
            'location' => 'Localização',
            'product' => 'Produto',
            'category' => 'Categoria',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Adicionar reabastecimento',

                'notification' => [
                    'title' => 'Reabastecimento adicionado',
                    'body' => 'O reabastecimento foi adicionado com sucesso.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Reabastecimento já existe',
                        'body' => 'Já existe um reabastecimento para esta configuração. Atualize o reabastecimento existente.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];
