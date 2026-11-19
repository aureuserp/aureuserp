<?php

return [
    'navigation' => [
        'title' => 'حركات الأصول',
    ],

    'label'        => 'حركة أصل',
    'plural-label' => 'حركات الأصول',

    'table' => [
        'columns' => [
            'occurred-at'        => 'التاريخ والوقت',
            'asset-tag'          => 'رقم الأصل',
            'asset-name'         => 'اسم الأصل',
            'type'               => 'نوع الحركة',
            'quantity'           => 'الكمية',
            'source-type'        => 'المصدر',
            'from'               => 'من',
            'to'                 => 'إلى',
            'condition'          => 'الحالة الفنية',
            'performed-by'       => 'نُفذ بواسطة',
            'expected-return-at' => 'تاريخ الإرجاع المتوقع',
            'note'               => 'ملاحظة',
            'attachments'        => 'المرفقات',
        ],
        'groups' => [
            'date'         => 'التاريخ',
            'type'         => 'نوع الحركة',
            'asset'        => 'الأصل',
            'condition'    => 'الحالة الفنية',
            'performed-by' => 'نُفذ بواسطة',
        ],
        'filters' => [
            'type'         => 'نوع الحركة',
            'product-type' => 'نوع المنتج',
            'source-type'  => 'المصدر',
            'condition'    => 'الحالة الفنية',
            'category'     => 'الفئة',
            'product'      => 'المنتج',
            'asset'        => 'الأصل',
            'warehouse'    => 'المستودع',
            'employee'     => 'الموظف',
            'department'   => 'القسم',
            'site'         => 'الموقع',
            'performed-by' => 'نُفذ بواسطة',
            'date-from'    => 'من تاريخ',
            'date-until'   => 'إلى تاريخ',
        ],
        'actions' => [
            'view' => 'عرض التفاصيل',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'تصدير',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'tabs' => [
                'all'           => 'الكل',
                'assets'        => 'الأصول',
                'consumables'   => 'المستهلكات',
                'issues'        => 'عمليات الصرف',
                'returns'       => 'عمليات الإرجاع',
                'transfers'     => 'عمليات النقل',
                'scrapped-lost' => 'شطب / مفقود',
                'damaged'       => 'تالف / سيء',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'details'     => 'بيانات الحركة',
            'attachments' => 'المرفقات والوثائق',
        ],
    ],
];
