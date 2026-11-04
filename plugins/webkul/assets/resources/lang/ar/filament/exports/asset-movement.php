<?php

return [
    'columns' => [
        'occurred-at'        => 'التاريخ والوقت',
        'asset-tag'          => 'رمز الأصل',
        'product'            => 'المنتج',
        'model'              => 'الموديل',
        'category'           => 'الفئة',
        'serial-no'          => 'الرقم التسلسلي',
        'type'               => 'نوع الحركة',
        'quantity'           => 'الكمية',
        'source-type'        => 'نوع المصدر',
        'from'               => 'من',
        'to'                 => 'إلى',
        'condition'          => 'الحالة الفنية',
        'expected-return-at' => 'تاريخ الإرجاع المتوقع',
        'performed-by'       => 'بواسطة',
        'note'               => 'ملاحظات',
        'company'            => 'الشركة',
    ],

    'options' => [
        'section'    => 'تصفية الحركات المصدّرة (اختياري)',
        'date-from'  => 'من تاريخ',
        'date-until' => 'إلى تاريخ',
        'types'      => 'أنواع الحركات',
        'sites'      => 'المواقع',
        'employees'  => 'الموظفون',
    ],

    'notification' => [
        'completed' => 'اكتملت عملية تصدير حركات الأصول وتم تصدير :count سطر.',
        'failed'    => 'فشل تصدير :count سطر.',
    ],
];
