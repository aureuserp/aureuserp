<?php

return [
    'navigation' => [
        'title' => 'Relatórios',
        'group' => 'Contabilidade',
    ],
    'common' => [
        'from-to' => ':report - De :from até :to',
        'expand-all' => 'Expandir tudo',
        'collapse-all' => 'Recolher tudo',
        'account' => 'Conta',
        'date' => 'Data',
        'communication' => 'Comunicação',
        'partner' => 'Parceiro',
        'journal' => 'Diário',
        'invoice-date' => 'Data da fatura',
        'due-date' => 'Data de vencimento',
        'debit' => 'Débito',
        'credit' => 'Crédito',
        'balance' => 'Saldo',
        'total' => 'Total',
        'opening-balance' => 'Saldo inicial',
        'initial-balance' => 'Saldo inicial',
        'end-balance' => 'Saldo final',
        'not-due' => 'A vencer',
        'no-data' => 'Nenhum dado disponível',
        'no-accounts-transactions' => 'Nenhuma conta com transações neste período',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Balanço patrimonial',
                'group' => 'Relatórios de demonstrações',
            ],
            'actions' => [
                'export-excel' => 'Exportar para Excel',
                'export-pdf' => 'Exportar para PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'journals' => 'Diários',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title' => 'ATIVOS',
                        'total-label' => 'Total de ATIVOS',
                        'subsections' => [
                            'current-assets' => [
                                'title' => 'Ativos circulantes',
                                'total-label' => 'Total de ativos circulantes',
                            ],
                            'fixed-assets' => [
                                'title' => 'Ativos imobilizados',
                                'total-label' => 'Total de ativos imobilizados',
                            ],
                            'non-current-assets' => [
                                'title' => 'Ativos não circulantes',
                                'total-label' => 'Total de ativos não circulantes',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title' => 'PASSIVOS',
                        'total-label' => 'Total de PASSIVOS',
                        'subsections' => [
                            'current-liabilities' => [
                                'title' => 'Passivos circulantes',
                                'total-label' => 'Total de passivos circulantes',
                            ],
                            'non-current-liabilities' => [
                                'title' => 'Passivos não circulantes',
                                'total-label' => 'Total de passivos não circulantes',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title' => 'PATRIMÔNIO LÍQUIDO',
                        'total-label' => 'Total do PATRIMÔNIO LÍQUIDO',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title' => 'Lucros não alocados',
                                'current-year' => 'Lucros não alocados do ano atual',
                                'previous-years' => 'Lucros não alocados de anos anteriores',
                                'total-label' => 'Total de lucros não alocados',
                            ],
                            'retained-earnings' => [
                                'title' => 'Lucros retidos',
                                'total-label' => 'Total de lucros retidos',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'PASSIVOS + PATRIMÔNIO LÍQUIDO',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Lucros e perdas',
                'group' => 'Relatórios de demonstrações',
            ],
            'actions' => [
                'export-excel' => 'Exportar para Excel',
                'export-pdf' => 'Exportar para PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'journals' => 'Diários',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title' => 'RECEITA',
                        'total-label' => 'Receita total',
                        'empty-message' => 'Nenhuma conta de receita com transações neste período',
                    ],
                    'expenses' => [
                        'title' => 'DESPESAS',
                        'total-label' => 'Total de despesas',
                        'empty-message' => 'Nenhuma conta de despesa com transações neste período',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Razão geral',
                'group' => 'Relatórios de auditoria',
            ],
            'actions' => [
                'export-excel' => 'Exportar para Excel',
                'export-pdf' => 'Exportar para PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'journals' => 'Diários',
            ],
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Balancete',
                'group' => 'Relatórios de auditoria',
            ],
            'actions' => [
                'export-excel' => 'Exportar para Excel',
                'export-pdf' => 'Exportar para PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'journals' => 'Diários',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Razão de parceiros',
                'group' => 'Relatórios de parceiros',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf' => 'Exportar PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'partners' => 'Parceiros',
                'journals' => 'Diários',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Contas a receber por vencimento',
                'group' => 'Relatórios de parceiros',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf' => 'Exportar PDF',
            ],
            'filters' => [
                'as-of' => 'Em',
                'based-on' => 'Baseado em',
                'period-length' => 'Duração do período (dias)',
                'journals' => 'Diários',
                'partners' => 'Parceiros',
                'entries' => 'Lançamentos',
                'options'       => [
                    'due-date' => 'Data de vencimento',
                    'invoice-date' => 'Data da fatura',
                    'days-30' => '30 dias',
                    'days-60' => '60 dias',
                    'days-90' => '90 dias',
                    'posted-entries' => 'Lançamentos publicados',
                    'all-entries' => 'Todos os lançamentos',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Contas a pagar por vencimento',
                'group' => 'Relatórios de parceiros',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf' => 'Exportar PDF',
            ],
            'filters' => [
                'as-of' => 'Em',
                'based-on' => 'Baseado em',
                'period-length' => 'Duração do período (dias)',
                'journals' => 'Diários',
                'partners' => 'Parceiros',
                'entries' => 'Lançamentos',
                'options'       => [
                    'due-date' => 'Data de vencimento',
                    'invoice-date' => 'Data da fatura',
                    'days-30' => '30 dias',
                    'days-60' => '60 dias',
                    'days-90' => '90 dias',
                    'posted-entries' => 'Lançamentos publicados',
                    'all-entries' => 'Todos os lançamentos',
                ],
            ],
        ],
    ],
];
