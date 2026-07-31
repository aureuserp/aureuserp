<?php

return [
    'title' => 'Fatura',

    'navigation' => [
        'title' => 'Faturas',
        'group' => 'Faturas',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'Geral',
                'fields' => [
                    'customer-invoice' => 'Nota de crédito de cliente',
                    'customer' => 'Cliente',
                    'invoice-date' => 'Data da fatura',
                    'due-date' => 'Data de vencimento',
                    'payment-term' => 'Condição de pagamento',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Linhas da fatura',

                'repeater' => [
                    'products' => [
                        'title' => 'Produtos',
                        'add-product' => 'Adicionar produto',

                        'fields' => [
                            'product' => 'Produto',
                            'quantity' => 'Quantidade',
                            'unit' => 'Unidade',
                            'taxes' => 'Impostos',
                            'discount-percentage' => 'Percentual de desconto',
                            'unit-price' => 'Preço unitário',
                            'sub-total' => 'Subtotal',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Outras informações',
                'fieldset' => [
                    'invoice' => [
                        'title' => 'Fatura',
                        'fields' => [
                            'customer-reference' => 'Referência do cliente',
                            'sales-person' => 'Vendedor',
                            'payment-reference' => 'Referência de pagamento',
                            'recipient-bank' => 'Banco do destinatário',
                            'delivery-date' => 'Data de entrega',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidade',

                        'fields' => [
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Local do Incoterm',
                            'payment-method' => 'Método de pagamento',
                            'auto-post' => 'Publicação automática',
                            'checked' => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Informações adicionais',
                        'fields' => [
                            'company' => 'Empresa',
                            'currency' => 'Moeda',
                        ],
                    ],

                    'marketing' => [
                        'title' => 'Marketing',
                        'fields' => [
                            'campaign' => 'Campanha',
                            'medium' => 'Médio',
                            'source' => 'Origem',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title' => 'Geral',
                'entries' => [
                    'customer-invoice' => 'Nota de crédito de cliente',
                    'customer' => 'Cliente',
                    'invoice-date' => 'Data da fatura',
                    'due-date' => 'Data de vencimento',
                    'payment-term' => 'Condição de pagamento',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Linhas da fatura',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product' => 'Produto',
                            'quantity' => 'Quantidade',
                            'unit' => 'Unidade de medida',
                            'taxes' => 'Impostos',
                            'discount-percentage' => 'Percentual de desconto',
                            'unit-price' => 'Preço unitário',
                            'sub-total' => 'Subtotal',
                            'total' => 'Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Outras informações',
                'fieldset' => [
                    'invoice' => [
                        'title' => 'Fatura',
                        'entries' => [
                            'customer-reference' => 'Referência do cliente',
                            'sales-person' => 'Vendedor',
                            'payment-reference' => 'Referência de pagamento',
                            'recipient-bank' => 'Banco do destinatário',
                            'delivery-date' => 'Data de entrega',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Contabilidade',

                        'fieldset' => [
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Local do Incoterm',
                            'payment-method' => 'Método de pagamento',
                            'auto-post' => 'Publicação automática',
                            'checked' => 'Verificado',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Informações adicionais',
                        'entries' => [
                            'company' => 'Empresa',
                            'currency' => 'Moeda',
                        ],
                    ],

                    'marketing' => [
                        'title' => 'Marketing',
                        'entries' => [
                            'campaign' => 'Campanha',
                            'medium' => 'Médio',
                            'source' => 'Origem',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],
];
