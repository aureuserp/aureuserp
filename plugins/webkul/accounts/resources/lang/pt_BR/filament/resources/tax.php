<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'name' => 'Nome',
                'tax-type' => 'Tipo de imposto',
                'tax-computation' => 'Cálculo do imposto',
                'tax-scope' => 'Escopo do imposto',
                'status' => 'Status',
                'amount' => 'Valor',
            ],

            'repeater' => [
                'invoice-repartition-lines' => [
                    'label' => 'Linhas de repartição da fatura',
                ],

                'refund-repartition-lines' => [
                    'label' => 'Linhas de repartição do reembolso',
                ],

                'fields' => [
                    'type' => 'Tipo',
                    'factor-percent' => 'Fator %',
                    'account' => 'Conta',
                ],
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Opções avançadas',

                    'fields' => [
                        'invoice-label' => 'Rótulo da fatura',
                        'tax-group' => 'Grupo de impostos',
                        'country' => 'País',
                        'include-in-price' => 'Incluído no preço',
                        'include-base-amount' => 'Afeta a base dos impostos subsequentes',
                        'is-base-affected' => 'Base afetada por impostos anteriores',
                    ],
                ],

                'fields' => [
                    'description' => 'Descrição',
                    'legal-notes' => 'Notas legais',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'amount-type' => 'Tipo de valor',
            'company' => 'Empresa',
            'tax-group' => 'Grupo de impostos',
            'country' => 'País',
            'tax-type' => 'Tipo de imposto',
            'tax-scope' => 'Escopo do imposto',
            'amount-type' => 'Tipo de valor',
            'invoice-label' => 'Rótulo da fatura',
            'tax-exigibility' => 'Exigibilidade do imposto',
            'price-include-override' => 'Substituição de inclusão no preço',
            'amount' => 'Valor',
            'status' => 'Status',
            'include-base-amount' => 'Incluir valor base',
            'is-base-affected' => 'A base é afetada',
        ],

        'groups' => [
            'name' => 'Nome',
            'company' => 'Empresa',
            'tax-group' => 'Grupo de impostos',
            'country' => 'País',
            'created-by' => 'Criado por',
            'type-tax-use' => 'Tipo de uso do imposto',
            'tax-scope' => 'Escopo do imposto',
            'amount-type' => 'Tipo de valor',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Imposto excluído',
                        'body' => 'O imposto foi excluído com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Imposto não pôde ser excluído',
                        'body' => 'O imposto não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Impostos excluídos',
                        'body' => 'Os impostos foram excluídos com sucesso.',
                    ],

                    'error' => [
                        'title' => 'Impostos não puderam ser excluídos',
                        'body' => 'Os impostos não podem ser excluídos porque estão em uso no momento.',
                    ],
                ],
            ],
        ],

        'pages' => [
            'create' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Linhas de repartição inválidas',
                    ],
                ],
            ],

            'edit' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Linhas de repartição inválidas',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name' => 'Nome',
                'tax-type' => 'Tipo de imposto',
                'tax-computation' => 'Cálculo do imposto',
                'tax-scope' => 'Escopo do imposto',
                'status' => 'Status',
                'amount' => 'Valor',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Opções avançadas',

                    'entries' => [
                        'invoice-label' => 'Rótulo da fatura',
                        'tax-group' => 'Grupo de impostos',
                        'country' => 'País',
                        'include-in-price' => 'Incluir no preço',
                        'include-base-amount' => 'Incluir valor base',
                        'is-base-affected' => 'A base é afetada',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title' => 'Descrição e notas legais da fatura',
                    'entries' => [
                        'description' => 'Descrição',
                        'legal-notes' => 'Notas legais',
                    ],
                ],
            ],
        ],
    ],

];
