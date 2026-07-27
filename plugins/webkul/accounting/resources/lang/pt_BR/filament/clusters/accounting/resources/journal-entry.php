<?php

return [
    'title' => 'Lançamentos contábeis',

    'navigation' => [
        'title' => 'Lançamentos contábeis',
    ],

    'record-sub-navigation' => [
        'payment' => 'Pagamento',
    ],

    'global-search' => [
        'number' => 'Número',
        'partner' => 'Parceiro',
        'date' => 'Data da fatura',
        'due-date' => 'Data de vencimento da fatura',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'Geral',

                'fields' => [
                    'reference' => 'Referência',
                    'accounting-date' => 'Data contábil',
                    'journal' => 'Diário',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Itens do lançamento',

                'repeater' => [
                    'title' => 'Itens',
                    'add-item' => 'Adicionar item',

                    'columns' => [
                        'account' => 'Conta',
                        'partner' => 'Parceiro',
                        'label' => 'Rótulo',
                        'amount-currency' => 'Valor (Moeda)',
                        'currency' => 'Moeda',
                        'taxes' => 'Impostos',
                        'debit' => 'Débito',
                        'credit' => 'Crédito',
                        'discount-amount-currency' => 'Valor do desconto (Moeda)',
                    ],

                    'fields' => [
                        'account' => 'Conta',
                        'partner' => 'Parceiro',
                        'label' => 'Rótulo',
                        'amount-currency' => 'Valor (Moeda)',
                        'currency' => 'Moeda',
                        'taxes' => 'Impostos',
                        'debit' => 'Débito',
                        'credit' => 'Crédito',
                        'discount-amount-currency' => 'Valor do desconto (Moeda)',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Outras informações',

                'fields' => [
                    'checked' => 'Verificado',
                    'company' => 'Empresa',
                    'fiscal-position' => 'Posição fiscal',
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
            'invoice-date' => 'Data da fatura',
            'date' => 'Data',
            'number' => 'Número',
            'partner' => 'Parceiro',
            'reference' => 'Referência',
            'journal' => 'Diário',
            'company' => 'Empresa',
            'total' => 'Total',
            'state' => 'Estado',
            'checked' => 'Verificado',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'partner' => 'Parceiro',
            'journal' => 'Diário',
            'state' => 'Estado',
            'payment-method' => 'Método de pagamento',
            'date' => 'Data',
            'invoice-date' => 'Data da fatura',
            'company' => 'Empresa',
        ],

        'filters' => [
            'number' => 'Número',
            'invoice-partner-display-name' => 'Nome de exibição do parceiro da fatura',
            'invoice-date' => 'Data da fatura',
            'invoice-due-date' => 'Data de vencimento da fatura',
            'invoice-origin' => 'Origem da fatura',
            'reference' => 'Referência',
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
                    'number' => 'Número',
                    'reference' => 'Referência',
                    'accounting-date' => 'Data contábil',
                    'journal' => 'Diário',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Itens do lançamento',

                'repeater' => [
                    'entries' => [
                        'account' => 'Conta',
                        'partner' => 'Parceiro',
                        'label' => 'Rótulo',
                        'currency' => 'Moeda',
                        'taxes' => 'Impostos',
                        'debit' => 'Débito',
                        'credit' => 'Crédito',
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
                            'fiscal-position' => 'Posição fiscal',
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

];
