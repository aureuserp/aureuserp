<?php

return [
    'navigation' => [
        'settings' => [
            'label' => 'الإعدادات',
            'group' => 'المشتريات',
        ],
    ],

    'documents' => [
        'purchase-order-title'  => 'أمر الشراء رقم :name',
        'buyer'                 => 'المشتري',
        'order-reference'       => 'مرجع الطلب',
        'order-deadline'        => 'الموعد النهائي للطلب',
        'expected-arrival'      => 'الوصول المتوقع',
        'description'           => 'الوصف',
        'product'               => 'المنتج',
        'quantity'              => 'الكمية',
        'unit'                  => 'الوحدة',
        'unit-price'            => 'سعر الوحدة',
        'discount'              => 'الخصم',
        'tax'                   => 'الضريبة',
        'taxes'                 => 'الضرائب',
        'amount'                => 'المبلغ',
        'untaxed-amount'        => 'المبلغ بدون ضريبة',
        'total'                 => 'الإجمالي',
        'quotation-valid-until' => 'عرض السعر صالح حتى:',
        'payment-terms'         => 'شروط الدفع:',
        'agreement-validity'    => 'صلاحية الاتفاقية',
        'contact'               => 'جهة الاتصال',
        'reference'             => 'المرجع',
        'terms-and-conditions'  => 'الشروط والأحكام:',
        'additional-terms'      => 'شروط إضافية:',
    ],

    'api' => [
        'purchase-order' => [
            'currency-locked'   => 'لا يمكن تغيير العملة بعد تأكيد أمر الشراء.',
            'order-date-locked' => 'لا يمكن تغيير الموعد النهائي للطلب بعد تأكيد أمر الشراء.',
        ],
    ],
];
