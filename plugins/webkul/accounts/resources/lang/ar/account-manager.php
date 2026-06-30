<?php

return [
    'post-action-validate' => [
        'customer-required'    => 'يرجى تقديم عميل صحيح للمتابعة مع التحقق من الفاتورة العميل.',
        'vendor-required'      => 'يرجى تقديم بائع صحيح للمتابعة مع التحقق من فاتورة البائع.',
        'bank-archived'        => 'البنك المحدد للشريك المرفق بهذه الفاتورة مؤرشف.',
        'negative-amount'      => 'لا يمكن تأكيد الفاتورة برقم سالب.',
        'date-required'        => 'يرجى تقديم تاريخ صحيح للفاتورة/الاسترجاع للمتابعة مع التحقق.',
        'currency-archived'    => 'لا يمكنك تأكيد فاتورة بعملة مؤرشفة.',
        'account-deprecated'   => 'واحد أو أكثر من البنود في هذه الفاتورة يستخدم حسابات مستهلكة.',
        'lines-required'       => 'يرجى إضافة سطر واحد على الأقل إلى الفاتورة.',
        'draft-state-required' => 'يمكن تأكيد الفواتير في حالة المسودة فقط.',
        'journal-archived'     => 'لا يمكنك تأكيد فاتورة بيوميات مؤرشفة.',
    ],

    'documents' => [
        'titles' => [
            'invoice'     => 'الفاتورة رقم :name',
            'bill'        => 'فاتورة المورد رقم :name',
            'refund'      => 'الاسترداد رقم :name',
            'credit-note' => 'إشعار الائتمان رقم :name',
        ],

        'labels' => [
            'invoice-date'          => 'تاريخ الفاتورة',
            'bill-date'             => 'التاريخ',
            'refund-date'           => 'تاريخ الاسترداد',
            'credit-note-date'      => 'تاريخ إشعار الائتمان',
            'source'                => 'المصدر',
            'due-date'              => 'تاريخ الاستحقاق',
            'product'               => 'المنتج',
            'quantity'              => 'الكمية',
            'unit'                  => 'الوحدة',
            'unit-price'            => 'سعر الوحدة',
            'subtotal'              => 'المجموع الفرعي',
            'tax'                   => 'الضريبة',
            'discount'              => 'الخصم',
            'grand-total'           => 'الإجمالي العام',
            'payment-information'   => 'معلومات الدفع',
            'payment-communication' => 'بيان الدفع',
            'account-details'       => 'على تفاصيل هذا الحساب:',
        ],
    ],
];
