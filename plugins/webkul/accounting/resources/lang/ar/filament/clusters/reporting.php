<?php

return [
    'navigation' => [
        'title' => 'التقارير',
        'group' => 'المحاسبة',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'الميزانية العمومية',
                'group' => 'تقارير البيانات المالية',
            ],
            'actions' => [
                'export-excel' => 'تصدير إلى إكسل',
                'export-pdf'   => 'تصدير إلى PDF',
            ],
            'filters' => [
                'date-range' => 'النطاق الزمني',
                'journals'   => 'دفاتر اليومية',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'الأصول',
                        'total-label' => 'إجمالي الأصول',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'الأصول المتداولة',
                                'total-label' => 'إجمالي الأصول المتداولة',
                            ],
                            'fixed-assets' => [
                                'title'       => 'الأصول الثابتة',
                                'total-label' => 'إجمالي الأصول الثابتة',
                            ],
                            'non-current-assets' => [
                                'title'       => 'الأصول غير المتداولة',
                                'total-label' => 'إجمالي الأصول غير المتداولة',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'الالتزامات',
                        'total-label' => 'إجمالي الالتزامات',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'الالتزامات المتداولة',
                                'total-label' => 'إجمالي الالتزامات المتداولة',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'الالتزامات غير المتداولة',
                                'total-label' => 'إجمالي الالتزامات غير المتداولة',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'حقوق الملكية',
                        'total-label' => 'إجمالي حقوق الملكية',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'الأرباح غير المخصصة',
                                'current-year'   => 'الأرباح غير المخصصة للسنة الحالية',
                                'previous-years' => 'الأرباح غير المخصصة للسنوات السابقة',
                                'total-label'    => 'إجمالي الأرباح غير المخصصة',
                            ],
                            'retained-earnings' => [
                                'title'       => 'الأرباح المحتجزة',
                                'total-label' => 'إجمالي الأرباح المحتجزة',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'الالتزامات + حقوق الملكية',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'الأرباح والخسائر',
                'group' => 'تقارير البيانات المالية',
            ],
            'actions' => [
                'export-excel' => 'تصدير إلى إكسل',
                'export-pdf'   => 'تصدير إلى PDF',
            ],
            'filters' => [
                'date-range' => 'النطاق الزمني',
                'journals'   => 'دفاتر اليومية',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'الإيرادات',
                        'total-label'   => 'إجمالي الإيرادات',
                        'empty-message' => 'لا توجد حسابات إيرادات بحركات خلال هذه الفترة',
                    ],
                    'expenses' => [
                        'title'         => 'المصروفات',
                        'total-label'   => 'إجمالي المصروفات',
                        'empty-message' => 'لا توجد حسابات مصروفات بحركات خلال هذه الفترة',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'دفتر الأستاذ العام',
                'group' => 'تقارير التدقيق',
            ],
            'actions' => [
                'export-excel' => 'تصدير إلى إكسل',
                'export-pdf'   => 'تصدير إلى PDF',
            ],
            'filters' => [
                'date-range' => 'النطاق الزمني',
                'journals'   => 'دفاتر اليومية',
            ],
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'ميزان المراجعة',
                'group' => 'تقارير التدقيق',
            ],
            'actions' => [
                'export-excel' => 'تصدير إلى إكسل',
                'export-pdf'   => 'تصدير إلى PDF',
            ],
            'filters' => [
                'date-range' => 'النطاق الزمني',
                'journals'   => 'دفاتر اليومية',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'دفتر الأستاذ للشركاء',
                'group' => 'تقارير الشركاء',
            ],
            'actions' => [
                'export-excel' => 'تصدير إكسل',
                'export-pdf'   => 'تصدير PDF',
            ],
            'filters' => [
                'date-range' => 'النطاق الزمني',
                'partners'   => 'الشركاء',
                'journals'   => 'دفاتر اليومية',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'أعمار الذمم المدينة',
                'group' => 'تقارير الشركاء',
            ],
            'actions' => [
                'export-excel' => 'تصدير إكسل',
                'export-pdf'   => 'تصدير PDF',
            ],
            'filters' => [
                'as-of'         => 'حتى تاريخ',
                'based-on'      => 'بناءً على',
                'period-length' => 'طول الفترة (بالأيام)',
                'journals'      => 'دفاتر اليومية',
                'partners'      => 'الشركاء',
                'entries'       => 'القيود',
                'options'       => [
                    'due-date'       => 'تاريخ الاستحقاق',
                    'invoice-date'   => 'تاريخ الفاتورة',
                    'days-30'        => '30 يوم',
                    'days-60'        => '60 يوم',
                    'days-90'        => '90 يوم',
                    'posted-entries' => 'القيود المرحلة',
                    'all-entries'    => 'كل القيود',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'أعمار الذمم الدائنة',
                'group' => 'تقارير الشركاء',
            ],
            'actions' => [
                'export-excel' => 'تصدير إكسل',
                'export-pdf'   => 'تصدير PDF',
            ],
            'filters' => [
                'as-of'         => 'حتى تاريخ',
                'based-on'      => 'بناءً على',
                'period-length' => 'طول الفترة (بالأيام)',
                'journals'      => 'دفاتر اليومية',
                'partners'      => 'الشركاء',
                'entries'       => 'القيود',
                'options'       => [
                    'due-date'       => 'تاريخ الاستحقاق',
                    'invoice-date'   => 'تاريخ الفاتورة',
                    'days-30'        => '30 يوم',
                    'days-60'        => '60 يوم',
                    'days-90'        => '90 يوم',
                    'posted-entries' => 'القيود المرحلة',
                    'all-entries'    => 'كل القيود',
                ],
            ],
        ],
    ],
];
