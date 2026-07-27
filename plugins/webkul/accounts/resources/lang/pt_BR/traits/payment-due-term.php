<?php

return [
    'form' => [
        'value' => 'Valor',
        'due' => 'Vencimento',
        'delay-due' => 'Prazo de vencimento',
        'delay-type' => 'Tipo de prazo',
        'days-on-the-next-month' => 'Dias no próximo mês',
        'days' => 'Dias',
        'payment-term' => 'Condição de pagamento',
    ],

    'table' => [
        'columns' => [
            'due' => 'Vencimento',
            'value' => 'Valor',
            'value-amount' => 'Valor',
            'after' => 'Depois',
            'delay-type' => 'Tipo de prazo',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Termo de vencimento do pagamento atualizado',
                    'body' => 'O termo de vencimento do pagamento foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Termo de vencimento do pagamento excluído',
                    'body' => 'O termo de vencimento do pagamento foi excluído com sucesso.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Termo de vencimento do pagamento criado',
                    'body' => 'O termo de vencimento do pagamento foi criado com sucesso.',
                ],
            ],
        ],
    ],
];
