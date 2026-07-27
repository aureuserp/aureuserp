<?php

return [
    'form' => [
        'fields' => [
            'tax-source' => 'Imposto de origem',
            'tax-destination' => 'Imposto de destino',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source' => 'Imposto de origem',
            'tax-destination' => 'Imposto de destino',
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

    'infolist' => [
        'entries' => [
            'tax-source' => 'Imposto de origem',
            'tax-destination' => 'Imposto de destino',
        ],
    ],
];
