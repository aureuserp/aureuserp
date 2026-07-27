<?php

return [
    'title' => 'Gerenciar contas padrão',

    'form' => [
        'exchange-difference-entries' => [
            'label' => 'Lançamentos de diferença cambial',

            'fields' => [
                'journal' => [
                    'label' => 'Diário',
                ],

                'gain' => [
                    'label' => 'Ganho',
                ],

                'loss' => [
                    'label' => 'Perda',
                ],
            ],
        ],

        'bank-transfer-and-payments' => [
            'label' => 'Transferência bancária e pagamentos',

            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'Conta transitória bancária',
                ],

                'transfer-account' => [
                    'label' => 'Conta de transferência',
                ],
            ],
        ],

        'product-accounts' => [
            'label' => 'Contas de produtos',

            'fields' => [
                'income-account' => [
                    'label' => 'Conta de receita',
                ],

                'expense-account' => [
                    'label' => 'Conta de despesa',
                ],
            ],
        ],
    ],
];
