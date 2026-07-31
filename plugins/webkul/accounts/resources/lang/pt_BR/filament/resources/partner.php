<?php

return [
    'title' => 'Parceiros',

    'navigation' => [
        'title' => 'Parceiros',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Vendas',

                        'fields' => [
                            'sales-person' => 'Vendedor',
                            'payment-terms' => 'Condições de pagamento',
                            'payment-method' => 'Método de pagamento',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Compras',

                        'fields' => [
                            'payment-terms' => 'Condições de pagamento',
                            'payment-method' => 'Método de pagamento',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Informações fiscais',

                        'fields' => [
                            'fiscal-position' => 'Posição fiscal',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title' => 'Faturamento',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Faturas de cliente',

                        'fields' => [
                            'invoice-sending-method' => 'Método de envio da fatura',
                            'invoice-edi-format-store' => 'Formato da fatura eletrônica',
                            'peppol-eas' => 'Endereço Peppol',
                            'endpoint' => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Lançamentos contábeis',

                        'fields' => [
                            'account-receivable' => 'Conta a receber',
                            'account-payable' => 'Conta a pagar',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automação',

                        'fields' => [
                            'auto-post-bills' => 'Publicar faturas de fornecedor automaticamente',
                            'ignore-abnormal-invoice-amount' => 'Ignorar valor anormal da fatura',
                            'ignore-abnormal-invoice-date' => 'Ignorar data anormal da fatura',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Notas internas',
            ],
        ],
    ],

    'infolist' => [

        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Vendas',

                        'entries' => [
                            'sales-person' => 'Vendedor',
                            'payment-terms' => 'Condições de pagamento',
                            'payment-method' => 'Método de pagamento',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Compras',

                        'entries' => [
                            'payment-terms' => 'Condições de pagamento',
                            'payment-method' => 'Método de pagamento',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Informações fiscais',

                        'entries' => [
                            'fiscal-position' => 'Posição fiscal',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title' => 'Faturamento',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Faturas de cliente',

                        'entries' => [
                            'invoice-sending-method' => 'Método de envio da fatura',
                            'invoice-edi-format-store' => 'Formato da fatura eletrônica',
                            'peppol-eas' => 'Endereço Peppol',
                            'endpoint' => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Lançamentos contábeis',

                        'entries' => [
                            'account-receivable' => 'Conta a receber',
                            'account-payable' => 'Conta a pagar',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automação',

                        'entries' => [
                            'auto-post-bills' => 'Publicar faturas de fornecedor automaticamente',
                            'ignore-abnormal-invoice-amount' => 'Ignorar valor anormal da fatura',
                            'ignore-abnormal-invoice-date' => 'Ignorar data anormal da fatura',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Notas internas',
            ],
        ],
    ],
];
