<?php

return [
    'title' => 'Fatura',

    'navigation' => [
        'title' => 'Faturas',
        'group' => 'Faturas',
    ],

    'global-search' => [
        'customer' => 'Cliente',
        'date' => 'Data',
        'due-date' => 'Data de vencimento',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'customer-invoice' => 'Fatura de cliente',
                    'customer' => 'Cliente',
                    'invoice-date' => 'Data da fatura',
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
                            'discount-percentage' => 'Desconto',
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
                            'company' => 'Empresa',
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Local do Incoterm',
                            'fiscal-position' => 'Posição fiscal',
                            'fiscal-position-tooltip' => 'Posições fiscais são usadas para adaptar impostos e contas com base na localização do cliente.',
                            'cash-rounding' => 'Método de arredondamento de caixa',
                            'cash-rounding-tooltip' => 'Especifica a menor unidade pagável em dinheiro da moeda.',
                            'payment-method' => 'Método de pagamento',
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
            'created-by' => 'Criado por',
            'customer' => 'Cliente',
            'invoice-date' => 'Data da fatura',
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
            'invoice-currency' => 'Moeda da fatura',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'name' => 'Nome',
            'invoice-partner-display-name' => 'Nome de exibição do parceiro da fatura',
            'invoice-date' => 'Data da fatura',
            'checked' => 'Verificado',
            'date' => 'Data',
            'invoice-due-date' => 'Data de vencimento da fatura',
            'invoice-origin' => 'Origem da fatura',
            'sales-person' => 'Vendedor',
            'currency' => 'Moeda',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'number' => 'Número',
            'invoice-partner-display-name' => 'Nome de exibição do parceiro da fatura',
            'invoice-date' => 'Data da fatura',
            'invoice-due-date' => 'Data de vencimento da fatura',
            'invoice-origin' => 'Origem da fatura',
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
                    'customer-invoice' => 'Fatura de cliente',
                    'customer' => 'Cliente',
                    'invoice-date' => 'Data da fatura',
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

                        'entries' => [
                            'company' => 'Empresa',
                            'incoterm' => 'Incoterm',
                            'incoterm-location' => 'Local do Incoterm',
                            'payment-method' => 'Método de pagamento',
                            'cash-rounding' => 'Método de arredondamento de caixa',
                            'fiscal-position' => 'Posição fiscal',
                            'auto-post' => 'Publicação automática',
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
                        'currency' => 'Moeda',
                        'due-date' => 'Data de vencimento',
                        'taxes' => 'Impostos',
                        'debit' => 'Débito',
                        'credit' => 'Crédito',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'actions' => [
            'reconcile' => [
                'label' => 'Adicionar',
            ],

            'unreconcile' => [
                'label' => 'Desvincular',
            ],
        ],
    ],

];
