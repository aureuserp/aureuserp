<?php

return [
    'global-search' => [
        'code' => 'Código',
        'type' => 'Tipo',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code' => 'Código',
                'account-name' => 'Nome da conta',
                'accounting' => 'Contabilidade',
                'account-type' => 'Tipo de conta',
                'parent-account' => 'Conta pai',
                'parent-account-helper' => 'Selecione uma conta existente para torná-la uma subconta.',
                'default-taxes' => 'Impostos padrão',
                'tags' => 'Tags',
                'journals' => 'Diários',
                'journals-helper' => 'Sugerido automaticamente com base no Tipo de conta selecionado. Você pode alterar a seleção.',
                'currency' => 'Moeda',
                'deprecated' => 'Obsoleto',
                'reconcile' => 'Permitir conciliação',
                'non-trade' => 'Não comercial',
                'companies' => 'Empresas',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code' => 'Código',
            'account-name' => 'Nome da conta',
            'account-type' => 'Conta',
            'parent-account' => 'Conta pai',
            'currency' => 'Moeda',
            'journals' => 'Diários',
            'reconcile' => 'Permitir conciliação',
        ],

        'grouping' => [
            'account-type' => 'Tipo de conta',
        ],

        'filters' => [
            'account-type' => 'Tipo de conta',
            'parent-account' => 'Conta pai',
            'allow-reconcile' => 'Permitir conciliação',
            'currency' => 'Moeda',
            'account-journals' => 'Diários',
            'non-trade' => 'Não comercial',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Conta atualizada',
                    'body' => 'A conta foi atualizada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Conta excluída',
                        'body' => 'A conta foi excluída com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Falha ao excluir conta',
                        'body' => 'A conta não pôde ser excluída porque possui itens de lançamento associados.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Contas excluídas',
                        'body' => 'As contas foram excluídas com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Falha ao excluir contas',
                        'body' => 'As contas não puderam ser excluídas porque possuem itens de lançamento associados.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code' => 'Código',
                'account-name' => 'Nome da conta',
                'accounting' => 'Contabilidade',
                'account-type' => 'Tipo de conta',
                'parent-account' => 'Conta pai',
                'sub-accounts' => 'Subcontas',
                'default-taxes' => 'Impostos padrão',
                'tags' => 'Tags',
                'journals' => 'Diários',
                'currency' => 'Moeda',
                'deprecated' => 'Obsoleto',
                'reconcile' => 'Conciliar',
                'non-trade' => 'Não comercial',
            ],
        ],
    ],
];
