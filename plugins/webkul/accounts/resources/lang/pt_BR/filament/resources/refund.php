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
                    'vendor-credit-note' => 'Nota de crédito de fornecedor',
                    'vendor' => 'Fornecedor',
                    'bill-date' => 'Data da fatura de fornecedor',
                    'bill-reference' => 'Referência da fatura de fornecedor',
                    'accounting-date' => 'Data contábil',
                    'payment-reference' => 'Referência de pagamento',
                    'recipient-bank' => 'Banco do destinatário',
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
                    'accounting' => [
                        'title' => 'Contabilidade',

                        'fields' => [
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Local do Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title' => 'Garantido',
                        'fields' => [
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
                    'vendor-invoice' => 'Fatura de fornecedor',
                    'vendor' => 'Fornecedor',
                    'bill-date' => 'Data da fatura de fornecedor',
                    'bill-reference' => 'Referência da fatura de fornecedor',
                    'accounting-date' => 'Data contábil',
                    'payment-reference' => 'Referência de pagamento',
                    'recipient-bank' => 'Banco do destinatário',
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

                        'entries' => [
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
                    'accounting' => [
                        'title' => 'Contabilidade',

                        'entries' => [
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Local do Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title' => 'Garantido',
                        'entries' => [
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
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],
];
