    <?php

    return [
        'setup' => [
            'title'               => 'المتابعون',
            'submit-action-title' => 'إضافة متابع',
            'tooltip'             => 'إضافة متابع',

            'form' => [
                'fields' => [
                    'recipients'  => 'المستلمون',
                    'notify-user' => 'إشعار المستخدم',
                    'add-a-note'  => 'إضافة ملاحظة',
                ],
            ],

            'actions' => [
                'notification' => [
                    'success' => [
                        'title' => 'تمت إضافة المتابع',
                        'body'  => 'تمت إضافة ":partner" كمتابع.',
                    ],

                    'partial_message' => [
                        'title'    => 'تم إرسال الرسالة مع ملاحظة',
                        'single'   => 'لم يتم إشعار :count متابع بسبب عدم وجود بريد إلكتروني: :names',
                        'multiple' => 'لم يتم إشعار :count متابعين بسبب عدم وجود بريد إلكتروني: :names',
                    ],

                    'error' => [
                        'title' => 'خطأ في إضافة المتابع',
                        'body'  => 'فشل في إضافة ":partner" كمتابع',
                    ],
                ],

                'mail' => [
                    'subject' => 'دعوة لمتابعة :model: :department',
                ],
            ],
        ],
    ];
