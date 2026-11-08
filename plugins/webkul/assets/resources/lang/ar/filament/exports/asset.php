<?php

return [
    'columns' => [
        'asset-tag'          => 'رمز الأصل',
        'product'            => 'المنتج',
        'product-type'       => 'نوع المنتج',
        'initial-quantity'   => 'الكمية الابتدائية',
        'quantity'           => 'الكمية',
        'model'              => 'الموديل',
        'category'           => 'الفئة',
        'serial-no'          => 'الرقم التسلسلي',
        'status'             => 'حالة الأصل',
        'condition'          => 'الحالة الفنية',
        'warehouse'          => 'المخزن / المستودع',
        'current-employee'   => 'الموظف الحالي',
        'department'         => 'القسم',
        'current-site'       => 'موقع العهدة الحالي',
        'site-type'          => 'نوع الموقع',
        'issued-at'          => 'تاريخ التسليم',
        'expected-return-at' => 'تاريخ الإرجاع المتوقع',
        'cost'               => 'التكلفة',
        'purchase-date'      => 'تاريخ الشراء',
        'warranty-until'     => 'الضمان حتى',
        'custody-note'       => 'ملاحظات العهدة',
        'note'               => 'ملاحظات',
        'company'            => 'الشركة',
        'created-at'         => 'تاريخ الإنشاء',
    ],

    'options' => [
        'section'    => 'تصفية الأصول المصدّرة (اختياري)',
        'products'   => 'المنتجات',
        'statuses'   => 'الحالات',
        'conditions' => 'الحالات الفنية',
        'categories' => 'الفئات',
        'warehouses' => 'المخازن',
        'sites'      => 'المواقع',
        'employees'  => 'الموظفون',
    ],

    'notification' => [
        'completed' => 'اكتملت عملية تصدير الأصول وتم تصدير :count سطر.',
        'failed'    => 'فشل تصدير :count سطر.',
    ],
];
