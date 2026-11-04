<?php

return [
    'columns' => [
        'name'                    => 'اسم المنتج',
        'type'                    => 'نوع المنتج',
        'category'                => 'الفئة',
        'model'                   => 'الموديل',
        'cost'                    => 'التكلفة',
        'assets-count'            => 'الوحدات المسجلة',
        'available-assets-count'  => 'المتاح بالمخازن',
        'held-assets-count'       => 'المصروف كعهدة',
        'terminated-assets-count' => 'المتلف / المفقود',
        'total-cost'              => 'إجمالي القيمة المالية',
        'is-active'               => 'نشط',
        'company'                 => 'الشركة',
        'created-at'              => 'تاريخ الإنشاء',
        'updated-at'              => 'تاريخ التحديث',
    ],

    'notification' => [
        'completed' => 'اكتملت عملية تصدير المنتجات وتم تصدير :count سطر.',
        'failed'    => 'فشل تصدير :count سطر.',
    ],
];
