<?php

return [
    'unknown'      => 'غير معروف',
    'other'        => 'أخرى',
    'share'        => 'الحصة',
    'status'       => 'الحالة',
    'stats'        => [
        'heading'              => 'المؤشرات الرئيسية',
        'draft-rfqs'           => 'طلبات عروض أسعار مسودة',
        'sent-rfqs'            => 'طلبات عروض أسعار مرسلة',
        'confirmed-orders'     => 'الأوامر المؤكدة',
        'total-purchase-value' => 'إجمالي قيمة الشراء',
        'increase'             => 'زيادة :percent%',
        'decrease'             => 'انخفاض :percent%',
        'no-change'            => 'لا تغيير',
    ],

    'trend'        => [
        'heading' => 'اتجاه أوامر الشراء حسب الحالة',
        'states'  => [
            'draft'    => 'مسودة',
            'sent'     => 'مرسل',
            'purchase' => 'شراء',
            'done'     => 'مكتمل',
            'canceled' => 'ملغى',
        ],
    ],

    'vendor-spend' => [
        'heading'         => 'حصة الإنفاق حسب المورد',
        'total-purchased' => 'إجمالي المشتريات',
    ],

    'top-orders'   => [
        'heading' => 'أعلى الأوامر',
        'columns' => [
            'reference'  => 'رقم الأمر',
            'vendor'     => 'المورد',
            'ordered-at' => 'تاريخ الطلب',
            'amount'     => 'المبلغ الإجمالي',
        ],
    ],

    'top-products' => [
        'heading' => 'أكثر المنتجات شراءً',
        'columns' => [
            'name'     => 'المنتج',
            'quantity' => 'إجمالي الكمية',
            'value'    => 'القيمة الإجمالية',
        ],
    ],
];
