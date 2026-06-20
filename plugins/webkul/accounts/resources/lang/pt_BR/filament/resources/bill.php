<?php

return [
    'title' => 'Fatura',

    'navigation' => [
        'title' => 'Faturas',
        'group' => 'Faturas',
    ],

    'global-search' => [
        'vendor' => 'Fornecedor',
        'date' => 'Data',
        'due-date' => 'Data de vencimento',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'Geral',
                'fields' => [
                    'vendor-bill' => 'Fatura de fornecedor',
                    'vendor' => 'Fornecedor',
                    'bill-date' => 'Data da fatura de fornecedor',
                    'bill-reference' => 'Referência da fatura de fornecedor',
                    'accounting-date' => 'Data contábil',
                    'payment-reference' => 'Referência de pagamento',
                    'recipient-bank' => 'Banco do destinatário',
                    'due-date' => 'Data de vencimento',
                    'payment-term' => 'Condição de pagamento',
                    'journal' => 'Diário',
                    'currency' => 'Moeda',
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

                        'columns' => [
                            'product' => 'Produto',
                            'quantity' => 'Quantidade',
                            'unit' => 'Unidade',
                            'taxes' => 'Impostos',
                            'discount-percentage' => 'Percentual de desconto',
                            'unit-price' => 'Preço unitário',
                            'sub-total' => 'Subtotal',
                        ],

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
                            'company' => 'Empresa',
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Local do Incoterm',
                            'payment-method' => 'Método de pagamento',
                            'fiscal-position' => 'Posição fiscal',
                            'fiscal-position-tooltip' => 'Posições fiscais são usadas para adaptar impostos e contas com base na localização do cliente.',
                            'cash-rounding' => 'Método de arredondamento de caixa',
                            'cash-rounding-tooltip' => 'Especifica a menor unidade pagável em dinheiro da moeda.',
                            'auto-post' => 'Publicação automática',
                            'checked' => 'Verificado',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Termos e condições',
            ],
        ],
    ],

    'table' => [
        'total' => 'Total',
        'columns' => [
            'number' => 'Número',
            'state' => 'Estado',
            'customer' => 'Cliente',
            'bill-date' => 'Data da fatura de fornecedor',
            'checked' => 'Verificado',
            'accounting-date' => 'Contabilidade',
            'due-date' => 'Data de vencimento',
            'source-document' => 'Documento de origem',
            'reference' => 'Referência',
            'sales-person' => 'Vendedor',
            'tax-excluded' => 'Imposto excluído',
            'tax' => 'Imposto',
            'total' => 'Total',
            'amount-due' => 'Valor devido',
            'bill-currency' => 'Moeda da fatura de fornecedor',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'name' => 'Nome',
            'bill-partner-display-name' => 'Nome de exibição do parceiro da fatura de fornecedor',
            'bill-date' => 'Data da fatura de fornecedor',
            'checked' => 'Verificado',
            'date' => 'Data',
            'bill-due-date' => 'Data de vencimento da fatura de fornecedor',
            'bill-origin' => 'Origem da fatura de fornecedor',
            'sales-person' => 'Vendedor',
            'currency' => 'Moeda',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'number' => 'Número',
            'bill-partner-display-name' => 'Nome de exibição do parceiro da fatura de fornecedor',
            'bill-date' => 'Data da fatura de fornecedor',
            'bill-due-date' => 'Data de vencimento da fatura de fornecedor',
            'bill-origin' => 'Origem da fatura de fornecedor',
            'reference' => 'Referência',
            'payment-reference' => 'Referência de pagamento',
            'narration' => 'Narração',
            'partner' => 'Parceiro',
            'journal' => 'Diário',
            'fiscal-position' => 'Posição fiscal',
            'currency' => 'Moeda',
            'company' => 'Empresa',
            'date' => 'Data contábil',
            'delivery-date' => 'Data de entrega',
            'amount-untaxed' => 'Valor sem impostos',
            'amount-tax' => 'Valor do imposto',
            'amount-total' => 'Valor total',
            'amount-residual' => 'Valor devido',
            'checked' => 'Verificado',
            'posted-before' => 'Publicado antes',
            'is-move-sent' => 'Enviado',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pagamento excluído',
                    'body' => 'O pagamento foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pagamentos excluídos',
                    'body' => 'Os pagamentos foram excluídos com sucesso.',
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
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
                    'journal' => 'Diário',
                    'currency' => 'Moeda',
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
                            'company' => 'Empresa',
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Local do Incoterm',
                            'payment-method' => 'Método de pagamento',
                            'checked' => 'Verificado',
                            'fiscal-position' => 'Posição fiscal',
                            'cash-rounding' => 'Método de arredondamento de caixa',
                            'checked' => 'Verificado',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Termos e condições',
            ],

            'journal-items' => [
                'title' => 'Itens do lançamento',

                'repeater' => [
                    'entries' => [
                        'account' => 'Conta',
                        'partner' => 'Parceiro',
                        'label' => 'Rótulo',
                        'due-date' => 'Data de vencimento',
                        'currency' => 'Moeda',
                        'taxes' => 'Impostos',
                        'debit' => 'Débito',
                        'credit' => 'Crédito',
                    ],
                ],
            ],
        ],
    ],
];
