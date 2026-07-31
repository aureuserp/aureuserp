<?php

return [
    'title' => 'Gerenciar contas padrão',

    'setup' => [
        'action'       => 'Configurar a contabilidade para esta empresa',
        'notice'       => 'A contabilidade ainda não está configurada para esta empresa. Use "Configurar contabilidade" acima para criar seu plano de contas, diários e configurações padrão.',
        'notification' => [
            'title' => 'Contabilidade configurada',
            'body'  => 'O plano de contas, os diários e as configurações padrão foram criados para esta empresa.',
        ],
    ],

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
