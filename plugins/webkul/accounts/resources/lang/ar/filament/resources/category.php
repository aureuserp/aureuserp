<?php

return [
    'form' => [
        'fieldsets' => [
            'account-properties' => [
                'label' => 'خصائص الحساب',

                'fields' => [
                    'income-account'                    => 'حساب الدخل',
                    'income-account-hint-tooltip'       => 'سيتم استخدام هذا الحساب عند التحقق من فاتورة العميل.',
                    'expense-account'                   => 'حساب النفقات',
                    'expense-account-hint-tooltip'      => 'يتم تسجيل النفقات عند التحقق من فاتورة البائع، باستثناء المحاسبة الأنجلو-ساكسونية مع التقييم الدائم للمخزون، حيث يتم الاعتراف بها بدلاً من ذلك عند التحقق من فاتورة العميل.',
                    'down-payment-account'              => 'حساب الدفعة المقدمة',
                    'down-payment-account-hint-tooltip' => 'حدد الحساب الذي سيتم تسجيل الدفعات المقدمة من هذه الفئة فيه.',
                ],
            ],
        ],
    ],
];
