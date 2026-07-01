<?php

return [
    'form' => [
        'fields' => [
            'name' => 'Nome',
            'rounding-precision' => 'Precisão de arredondamento',
            'rounding-strategy' => 'Estratégia de arredondamento',
            'profit-account' => 'Conta de lucro',
            'loss-account' => 'Conta de perda',
            'rounding-method' => 'Método de arredondamento',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'rounding-strategy' => 'Estratégia de arredondamento',
            'rounding-method' => 'Método de arredondamento',
            'created-by' => 'Criado por',
            'profit-account' => 'Conta de lucro',
            'loss-account' => 'Conta de perda',
        ],

        'groups' => [
            'name' => 'Nome',
            'rounding-strategy' => 'Estratégia de arredondamento',
            'rounding-method' => 'Método de arredondamento',
            'created-by' => 'Criado por',
            'profit-account' => 'Conta de lucro',
            'loss-account' => 'Conta de perda',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Arredondamento de caixa excluído',
                    'body' => 'O arredondamento de caixa foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Arredondamento de caixa excluído',
                    'body' => 'O arredondamento de caixa foi excluído com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'rounding-precision' => 'Precisão de arredondamento',
            'rounding-strategy' => 'Estratégia de arredondamento',
            'profit-account' => 'Conta de lucro',
            'loss-account' => 'Conta de perda',
            'rounding-method' => 'Método de arredondamento',
        ],
    ],
];
