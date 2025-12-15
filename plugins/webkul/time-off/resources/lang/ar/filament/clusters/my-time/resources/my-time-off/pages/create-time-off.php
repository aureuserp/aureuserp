<?php

return [
    'notification' => [
        'success' => [
            'title' => 'تم إنشاء الإجازة',
            'body'  => 'تم إنشاء الإجازة بنجاح.',
        ],

        'overlap' => [
            'title' => 'طلب إجازة متداخل',
            'body'  => 'تواريخ الإجازة المحددة متداخلة مع طلب موجود. يرجى اختيار تواريخ مختلفة.',
        ],

        'warning' => [
            'title' => 'ليس لديك حساب موظف',
            'body'  => 'ليس لديك حساب موظف. يرجى التواصل مع المسؤول.',
        ],

        'invalid_half_day_leave' => [
            'title' => 'طلب إجازة غير صالح',
            'body'  => 'يمكن تطبيق إجازة نصف يوم ليوم واحد فقط.',
        ],

        'leave_request_denied_no_allocation' => [
            'title' => 'تم رفض طلب الإجازة',
            'body'  => 'ليس لديك أي إجازة مخصصة لـ :leaveType.',
        ],

        'leave_request_denied_insufficient_balance' => [
            'title' => 'تم رفض طلب الإجازة',
            'body'  => 'رصيد الإجازة غير كافٍ. لديك :available_balance يوم/أيام متاحة. المطلوب: :requested_days يوم/أيام.',
        ],
    ],
];
