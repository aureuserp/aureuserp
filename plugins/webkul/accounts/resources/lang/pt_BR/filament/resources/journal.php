<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Lançamentos contábeis',

                'field-set' => [
                    'accounting-information' => [
                        'title' => 'Informações contábeis',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Sequência dedicada de nota de crédito',
                            'dedicated-payment-sequence' => 'Sequência dedicada de pagamento',
                            'sort-code-placeholder' => 'Informe o código do diário',
                            'sort-code' => 'Ordenar',
                            'currency' => 'Moeda',
                            'color' => 'Cor',
                            'default-account' => 'Conta padrão',
                            'profit-account' => 'Conta de lucro',
                            'loss-account' => 'Conta de perda',
                            'suspense-account' => 'Conta transitória',
                            'bank-account' => 'Conta bancária',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Número da conta bancária',
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'Pagamentos recebidos',
                'add-action-label' => 'Adicionar linha',

                'fields' => [
                    'payment-method' => 'Método de pagamento',
                    'display-name' => 'Nome de exibição',
                    'account-number' => 'Contas de recebimentos pendentes',
                    'relation-notes' => 'Notas de relacionamento',
                    'relation-notes-placeholder' => 'Informe os detalhes do relacionamento',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'Pagamentos enviados',
                'add-action-label' => 'Adicionar linha',

                'fields' => [
                    'payment-method' => 'Método de pagamento',
                    'display-name' => 'Nome de exibição',
                    'account-number' => 'Contas de pagamentos pendentes',
                    'relation-notes' => 'Notas de relacionamento',
                    'relation-notes-placeholder' => 'Informe os detalhes do relacionamento',
                ],
            ],

            'advanced-settings' => [
                'title' => 'Configurações avançadas',

                'fields' => [
                    'allowed-accounts' => 'Contas permitidas',
                    'control-access' => 'Controle de acesso',
                    'payment-communication' => 'Comunicação de pagamento',
                    'auto-check-on-post' => 'Verificação automática ao publicar',
                    'communication-type' => 'Tipo de comunicação',
                    'communication-standard' => 'Padrão de comunicação',
                ],
            ],
        ],

        'general' => [
            'title' => 'Informações gerais',

            'fields' => [
                'name' => 'Nome',
                'type' => 'Tipo',
                'company' => 'Empresa',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'type' => 'Tipo',
            'code' => 'Código',
            'currency' => 'Moeda',
            'created-by' => 'Criado por',
            'status' => 'Status',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Diário excluído',
                        'body' => 'O diário foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Falha ao excluir diário',
                        'body' => 'O diário não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Diário excluído',
                        'body' => 'O diário foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Falha ao excluir diários',
                        'body' => 'Os diários não podem ser excluídos porque estão em uso no momento.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Lançamentos contábeis',

                'field-set' => [
                    'accounting-information' => [
                        'title' => 'Informações contábeis',

                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Sequência dedicada de nota de crédito',
                            'dedicated-payment-sequence' => 'Sequência dedicada de pagamento',
                            'sort-code-placeholder' => 'Informe o código do diário',
                            'sort-code' => 'Ordenar',
                            'currency' => 'Moeda',
                            'color' => 'Cor',
                            'default-account' => 'Conta padrão',
                            'profit-account' => 'Conta de lucro',
                            'loss-account' => 'Conta de perda',
                            'suspense-account' => 'Conta transitória',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Número da conta bancária',

                        'entries' => [
                            'account-number' => 'Número da conta',
                        ],
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'Pagamentos recebidos',

                'entries' => [
                    'payment-method' => 'Método de pagamento',
                    'display-name' => 'Nome de exibição',
                    'account-number' => 'Contas de recebimentos pendentes',
                    'relation-notes' => 'Notas de relacionamento',
                    'relation-notes-placeholder' => 'Informe os detalhes do relacionamento',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'Pagamentos enviados',

                'entries' => [
                    'payment-method' => 'Método de pagamento',
                    'display-name' => 'Nome de exibição',
                    'account-number' => 'Contas de pagamentos pendentes',
                    'relation-notes' => 'Notas de relacionamento',
                    'relation-notes-placeholder' => 'Informe os detalhes do relacionamento',
                ],
            ],

            'advanced-settings' => [
                'title' => 'Configurações avançadas',

                'allowed-accounts' => [
                    'title' => 'Contas permitidas',

                    'entries' => [
                        'allowed-accounts' => 'Contas permitidas',
                        'control-access' => 'Controle de acesso',
                        'auto-check-on-post' => 'Verificação automática ao publicar',
                    ],
                ],

                'payment-communication'  => [
                    'title' => 'Comunicação de pagamento',

                    'entries' => [
                        'communication-type' => 'Tipo de comunicação',
                        'communication-standard' => 'Padrão de comunicação',
                    ],
                ],
            ],
        ],

        'general' => [
            'title' => 'Informações gerais',

            'entries' => [
                'name' => 'Nome',
                'type' => 'Tipo',
                'company' => 'Empresa',
            ],
        ],
    ],

];
