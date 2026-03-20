<?php

return [
    'columns' => [
        'number'          => 'الرقم',
        'state'           => 'الحالة',
        'customer'        => 'العميل',
        'bill-date'       => 'تاريخ الفاتورة',
        'due-date'        => 'تاريخ الاستحقاق',
        'tax-excluded'    => 'بدون ضريبة',
        'tax'             => 'الضريبة',
        'total'           => 'الإجمالي',
        'amount-due'      => 'المبلغ المستحق',
        'payment-state'   => 'حالة الدفع',
        'checked'         => 'تم التحقق',
        'accounting-date' => 'تاريخ المحاسبة',
        'source-document' => 'المستند المصدر',
        'reference'       => 'المرجع',
        'sales-person'    => 'مندوب المبيعات',
        'bill-currency'   => 'عملة الفاتورة',
    ],

    'values' => [
        'yes' => 'نعم',
        'no'  => 'لا',
    ],

    'notification' => [
        'completed' => 'اكتملت عملية تصدير الفاتورة وتم تصدير :count سطر.',
        'failed'    => 'فشل تصدير :count سطر.',
    ],
];
