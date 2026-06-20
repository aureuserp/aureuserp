<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term' => 'Condição de pagamento',
                'early-discount' => 'Desconto antecipado',
                'discount-days-prefix' => 'se pago em até',
                'discount-days-suffix' => 'dias',
                'reduced-tax' => 'Imposto reduzido',
                'note' => 'Nota',
                'status' => 'Status',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Termos de vencimento',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value' => 'Valor',
                            'due' => 'Vencimento',
                            'delay-type' => 'Tipo de prazo',
                            'days-on-the-next-month' => 'Dias no próximo mês',
                            'days' => 'Dias',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term' => 'Condição de pagamento',
            'company' => 'Empresa',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'company-name' => 'Nome da empresa',
            'discount-days' => 'Dias de desconto',
            'early-pay-discount' => 'Desconto por pagamento antecipado',
            'payment-term' => 'Condição de pagamento',
            'display-on-invoice' => 'Exibir na fatura',
            'early-discount' => 'Desconto antecipado',
            'discount-percentage' => 'Percentual de desconto',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Condição de pagamento restaurada',
                    'body' => 'A condição de pagamento foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Condição de pagamento excluída',
                    'body' => 'A condição de pagamento foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Condição de pagamento excluída permanentemente',
                        'body' => 'A condição de pagamento foi excluída permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Falha na exclusão permanente da condição de pagamento',
                        'body' => 'A condição de pagamento não pôde ser excluída permanentemente porque possui lançamentos contábeis associados.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Condições de pagamento restauradas',
                    'body' => 'As condições de pagamento foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Condições de pagamento excluídas',
                    'body' => 'As condições de pagamento foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Condições de pagamento excluídas permanentemente',
                        'body' => 'As condições de pagamento foram excluídas permanentemente com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Falha na exclusão permanente das condições de pagamento',
                        'body' => 'As condições de pagamento não puderam ser excluídas permanentemente porque possuem lançamentos contábeis associados.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term' => 'Condição de pagamento',
                'early-discount' => 'Desconto antecipado',
                'discount-percentage' => 'Percentual de desconto',
                'discount-days-prefix' => 'se pago em até',
                'discount-days-suffix' => 'dias',
                'reduced-tax' => 'Imposto reduzido',
                'note' => 'Nota',
                'status' => 'Status',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Termos de vencimento',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value' => 'Valor',
                            'due' => 'Vencimento',
                            'delay-type' => 'Tipo de prazo',
                            'days-on-the-next-month' => 'Dias no próximo mês',
                            'days' => 'Dias',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
