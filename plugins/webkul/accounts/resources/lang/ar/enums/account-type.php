<?php

return [
    'assets' => [
        'label'   => 'الأصول',
        'options' => [
            'receivable'  => 'الذمم المدينة',
            'cash'        => 'البنك والنقد',
            'current'     => 'الأصول المتداولة',
            'non-current' => 'الأصول غير المتداولة',
            'prepayments' => 'المدفوعات المقدمة',
            'fixed'       => 'الأصول الثابتة',
        ],
    ],

    'liabilities' => [
        'label'   => 'الالتزامات',
        'options' => [
            'payable'     => 'الذمم الدائنة',
            'credit-card' => 'بطاقة الائتمان',
            'current'     => 'الالتزامات المتداولة',
            'non-current' => 'الالتزامات غير المتداولة',
        ],
    ],

    'equity' => [
        'label'   => 'حقوق الملكية',
        'options' => [
            'equity'     => 'حقوق الملكية',
            'unaffected' => 'أرباح السنة الحالية',
        ],
    ],

    'income' => [
        'label'   => 'الإيرادات',
        'options' => [
            'income' => 'الإيرادات',
            'other'  => 'إيرادات أخرى',
        ],
    ],

    'expenses' => [
        'label'   => 'المصروفات',
        'options' => [
            'expense'      => 'المصروفات',
            'depreciation' => 'الإهلاك',
            'direct-cost'  => 'تكلفة الإيرادات',
        ],
    ],

    'off-balance' => [
        'label'   => 'خارج الميزانية',
        'options' => [
            'off-balance' => 'خارج الميزانية',
        ],
    ],
];
