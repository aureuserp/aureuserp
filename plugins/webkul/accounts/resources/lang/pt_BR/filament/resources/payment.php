<?php

return [
    'title' => 'Pagamento',

    'navigation' => [
        'title' => 'Pagamentos',
        'group' => 'Faturas',
    ],

    'global-search' => [
        'partner' => 'Parceiro',
        'amount' => 'Valor',
        'date' => 'Data',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type' => 'Tipo de pagamento',
                'memo' => 'Memorando',
                'date' => 'Data',
                'amount' => 'Valor',
                'currency' => 'Moeda',
                'payment-method' => 'Método de pagamento',
                'customer' => 'Cliente',
                'vendor' => 'Fornecedor',
                'journal' => 'Diário',
                'customer-bank-account' => 'Conta bancária do cliente',
                'vendor-bank-account' => 'Conta bancária do fornecedor',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'date' => 'Data',
            'journal' => 'Diário',
            'payment-method' => 'Método de pagamento',
            'partner' => 'Parceiro',
            'amount-currency' => 'Valor (Moeda)',
            'amount' => 'Valor',
            'state' => 'Estado',
            'company' => 'Empresa',
            'currency' => 'Moeda',
            'created-by' => 'Criado por',
        ],

        'groups' => [
            'name' => 'Nome',
            'company' => 'Empresa',
            'journal' => 'Diário',
            'partner' => 'Parceiro',
            'payment-method-line' => 'Linha do método de pagamento',
            'payment-method' => 'Método de pagamento',
            'partner-bank-account' => 'Conta bancária do parceiro',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'company' => 'Empresa',
            'journal' => 'Diário',
            'customer-bank-account' => 'Conta bancária do cliente',
            'payment-method' => 'Método de pagamento',
            'currency' => 'Moeda',
            'partner' => 'Parceiro',
            'payment-method-line' => 'Linha do método de pagamento',
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
        'sections' => [
            'payment-information' => [
                'title' => 'Informações de pagamento',
                'entries' => [
                    'state' => 'Estado',
                    'vendor' => 'Fornecedor',
                    'customer' => 'Cliente',
                    'payment-type' => 'Tipo de pagamento',
                    'journal' => 'Diário',
                    'customer-bank-account' => 'Conta bancária do cliente',
                    'vendor-bank-account' => 'Conta bancária do fornecedor',
                    'amount' => 'Valor',
                    'payment-method' => 'Método de pagamento',
                    'date' => 'Data',
                    'memo' => 'Memorando',
                ],
            ],
        ],
    ],

];
