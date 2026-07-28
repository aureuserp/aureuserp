<?php

return [
    'navigation' => [
        'title' => 'Contas bancárias',
        'group' => 'Bancos',
    ],

    'form' => [
        'account-number' => 'Número da conta',
        'bank'               => [
            'title' => 'Banco',
            'sections' => [
                'general' => [
                    'title' => 'Geral',

                    'fields' => [
                        'name' => 'Nome',
                        'code' => 'Código identificador do banco',
                        'email' => 'E-mail',
                        'phone' => 'Telefone',
                    ],
                ],

                'address' => [
                    'title' => 'Endereço',

                    'fields' => [
                        'address' => 'Endereço',
                        'city' => 'Cidade',
                        'street1' => 'Rua 1',
                        'street2' => 'Rua 2',
                        'state' => 'Estado',
                        'zip' => 'CEP',
                        'country' => 'País',
                    ],
                ],
            ],
        ],

        'account-holder' => 'Titular da conta',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Número da conta',
            'bank' => 'Banco',
            'account-holder' => 'Titular da conta',
            'send-money' => 'Pode enviar dinheiro',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
            'deleted-at' => 'Excluído em',
        ],

        'filters' => [
            'bank' => 'Banco',
            'account-holder' => 'Titular da conta',
            'creator' => 'Criador',
            'can-send-money' => 'Pode enviar dinheiro',
        ],

        'groups' => [
            'bank' => 'Banco',
            'can-send-money' => 'Pode enviar dinheiro',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Conta bancária atualizada',
                    'body' => 'A conta bancária foi atualizada com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Conta bancária restaurada',
                    'body' => 'A conta bancária foi restaurada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Conta bancária excluída',
                    'body' => 'A conta bancária foi excluída com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Conta bancária excluída permanentemente',
                    'body' => 'A conta bancária foi excluída permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Contas bancárias restauradas',
                    'body' => 'As contas bancárias foram restauradas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Contas bancárias excluídas',
                    'body' => 'As contas bancárias foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Contas bancárias excluídas permanentemente',
                    'body' => 'As contas bancárias foram excluídas permanentemente com sucesso.',
                ],
            ],
        ],
    ],
];
