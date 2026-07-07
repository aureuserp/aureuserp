<?php

return [
    'navigation' => [
        'title' => 'Informes',
    ],
    'common' => [
        'from-to' => ':report - Desde :from hasta :to',
        'expand-all' => 'Expandir todo',
        'collapse-all' => 'Contraer todo',
        'account' => 'Cuenta',
        'date' => 'Fecha',
        'communication' => 'Comunicación',
        'partner' => 'Contacto',
        'journal' => 'Diario',
        'invoice-date' => 'Fecha de factura',
        'due-date' => 'Fecha de vencimiento',
        'debit' => 'Débito',
        'credit' => 'Crédito',
        'balance' => 'Saldo',
        'total' => 'Total',
        'opening-balance' => 'Saldo de apertura',
        'initial-balance' => 'Saldo inicial',
        'end-balance' => 'Saldo final',
        'not-due' => 'No vencido',
        'no-data' => 'No hay datos disponibles',
        'no-accounts-transactions' => 'No hay cuentas con transacciones en este período',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Balance general',
                'group' => 'Informes de estados',
            ],
            'actions' => [
                'export-excel' => 'Exportar a Excel',
                'export-pdf'   => 'Exportar a PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'journals'   => 'Diarios',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'ACTIVOS',
                        'total-label' => 'Total ACTIVOS',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Activos corrientes',
                                'total-label' => 'Total activos corrientes',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Activos fijos',
                                'total-label' => 'Total activos fijos',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Activos no corrientes',
                                'total-label' => 'Total activos no corrientes',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'PASIVOS',
                        'total-label' => 'Total PASIVOS',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Pasivos corrientes',
                                'total-label' => 'Total pasivos corrientes',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Pasivos no corrientes',
                                'total-label' => 'Total pasivos no corrientes',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'PATRIMONIO',
                        'total-label' => 'Total PATRIMONIO',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Ganancias no asignadas',
                                'current-year'   => 'Ganancias no asignadas del año actual',
                                'previous-years' => 'Ganancias no asignadas de años anteriores',
                                'total-label'    => 'Total ganancias no asignadas',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Ganancias retenidas',
                                'total-label' => 'Total ganancias retenidas',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'PASIVOS + PATRIMONIO',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Pérdidas y ganancias',
                'group' => 'Informes de estados',
            ],
            'actions' => [
                'export-excel' => 'Exportar a Excel',
                'export-pdf'   => 'Exportar a PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'journals'   => 'Diarios',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'INGRESOS',
                        'total-label'   => 'Total ingresos',
                        'empty-message' => 'No hay cuentas de ingresos con transacciones en este período',
                    ],
                    'expenses' => [
                        'title'         => 'GASTOS',
                        'total-label'   => 'Total gastos',
                        'empty-message' => 'No hay cuentas de gastos con transacciones en este período',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Libro mayor general',
                'group' => 'Informes de auditoría',
            ],
            'actions' => [
                'export-excel' => 'Exportar a Excel',
                'export-pdf'   => 'Exportar a PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'journals'   => 'Diarios',
            ],
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Balance de comprobación',
                'group' => 'Informes de auditoría',
            ],
            'actions' => [
                'export-excel' => 'Exportar a Excel',
                'export-pdf'   => 'Exportar a PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'journals'   => 'Diarios',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Libro mayor de contactos',
                'group' => 'Informes de contactos',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf'   => 'Exportar PDF',
            ],
            'filters' => [
                'date-range' => 'Rango de fechas',
                'partners'   => 'Contactos',
                'journals'   => 'Diarios',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Antigüedad de cuentas por cobrar',
                'group' => 'Informes de contactos',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf'   => 'Exportar PDF',
            ],
            'filters' => [
                'as-of'         => 'A fecha de',
                'based-on'      => 'Basado en',
                'period-length' => 'Duración del período (días)',
                'journals'      => 'Diarios',
                'partners'      => 'Contactos',
                'entries'       => 'Asientos',
                'options'       => [
                    'due-date'       => 'Fecha de vencimiento',
                    'invoice-date'   => 'Fecha de factura',
                    'days-30'        => '30 días',
                    'days-60'        => '60 días',
                    'days-90'        => '90 días',
                    'posted-entries' => 'Asientos contabilizados',
                    'all-entries'    => 'Todos los asientos',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Antigüedad de cuentas por pagar',
                'group' => 'Informes de contactos',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf'   => 'Exportar PDF',
            ],
            'filters' => [
                'as-of'         => 'A fecha de',
                'based-on'      => 'Basado en',
                'period-length' => 'Duración del período (días)',
                'journals'      => 'Diarios',
                'partners'      => 'Contactos',
                'entries'       => 'Asientos',
                'options'       => [
                    'due-date'       => 'Fecha de vencimiento',
                    'invoice-date'   => 'Fecha de factura',
                    'days-30'        => '30 días',
                    'days-60'        => '60 días',
                    'days-90'        => '90 días',
                    'posted-entries' => 'Asientos contabilizados',
                    'all-entries'    => 'Todos los asientos',
                ],
            ],
        ],
    ],
];
