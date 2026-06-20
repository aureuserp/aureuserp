<?php

return [
    'form' => [
        'fieldsets' => [
            'account-properties' => [
                'label' => 'Propriedades da conta',

                'fields' => [
                    'income-account' => 'Conta de receita',
                    'income-account-hint-tooltip' => 'Esta conta será usada ao validar uma fatura de cliente.',
                    'expense-account' => 'Conta de despesa',
                    'expense-account-hint-tooltip' => 'A despesa é registrada quando uma fatura de fornecedor é validada, exceto na contabilidade anglo-saxônica com avaliação de estoque perpétuo, em que ela é reconhecida como Custo das Mercadorias Vendidas quando a fatura de cliente é validada.',
                    'down-payment-account' => 'Conta de adiantamento',
                    'down-payment-account-hint-tooltip' => 'Selecione a conta em que os adiantamentos desta categoria serão registrados.',
                ],
            ],
        ],
    ],
];
