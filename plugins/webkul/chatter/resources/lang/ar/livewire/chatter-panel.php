<?php

return [
    'placeholders' => [
        'no-record-found' => 'لم يتم العثور على سجل.',
        'loading'         => 'جاري تحميل المحادثات...',
    ],

    'activity-infolist' => [
        'title' => 'الأنشطة',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'إلغاء النشاط',
    ],

    'delete-message-action' => [
        'title' => 'حذف الرسالة',
    ],

    'edit-activity' => [
        'title' => 'تعديل النشاط',

        'form' => [
            'fields' => [
                'activity-plan' => 'خطة النشاط',
                'plan-date'     => 'تاريخ الخطة',
                'plan-summary'  => 'ملخص الخطة',
                'activity-type' => 'نوع النشاط',
                'due-date'      => 'تاريخ الاستحقاق',
                'summary'       => 'الملخص',
                'assigned-to'   => 'مُعيَّن إلى',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'تم تحديث النشاط',
                    'body'  => 'تم تحديث النشاط بنجاح.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">الملاحظة الأصلية</span>: :body</div>',
        'original-note' => '<br><div><span class="font-bold">الملاحظة الأصلية</span>: :body</div>',
        'feedback'      => '<div><span class="font-bold">الملاحظات</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title' => 'تعيين كمكتمل',
        'form'  => [
            'fields' => [
                'feedback' => 'الملاحظات',
            ],
        ],

        'footer-actions' => [
            'label' => 'إتمام وجدولة التالي',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'تم تعيين النشاط كمكتمل',
                        'body'  => 'تم تعيين النشاط كمكتمل بنجاح.',
                    ],
                ],
            ],
        ],
    ],
];
