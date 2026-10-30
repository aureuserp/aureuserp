<?php

return [
    'navigation' => [
        'title' => 'عروض أسعاري',
    ],

    'pages' => [
        'list' => [
            'title' => 'عروض أسعاري',
        ],
    ],

    'tabs' => [
        'opportunity' => 'الفرصة',
    ],

    'board' => [
        'unauthorized' => 'لا يمكنك نقل عرض السعر هذا إلى ذلك العمود.',

        'card' => [
            'open'        => 'فتح :name',
            'customer'    => 'العميل',
            'total'       => 'الإجمالي',
            'date'        => 'تاريخ الطلب',
            'salesperson' => 'مندوب المبيعات',
        ],
    ],

    'actions' => [
        'mark-as-sent' => [
            'label'        => 'تحديد عرض السعر كمرسل',
            'notification' => 'تم تحديد :count عرض سعر كمرسل.',
        ],

        'send-email' => [
            'label'        => 'إرسال بريد إلكتروني',
            'description'  => 'يُرسل كل عرض سعر محدد إلى عميله بالبريد مع ملف PDF مرفق. تُحدَّد المسودات كمرسلة.',
            'submit'       => 'إرسال',
            'subject'      => 'عرض سعر :customer (مرجع :name)',
            'body'         => 'عزيزي :customer،<br><br>عرض السعر <strong>:name</strong> بقيمة <strong>:amount</strong> جاهز للمراجعة.<br><br>لا تتردد في التواصل معنا إذا كانت لديك أي أسئلة.',
            'notification' => 'تم إرسال :sent عرض سعر، وتخطي :skipped (ليس عرض سعر أو بلا بريد للعميل أو بلا صلاحية).',
        ],

        'confirm' => [
            'label'        => 'تأكيد',
            'description'  => 'ستصبح عروض الأسعار المحددة أوامر بيع.',
            'notification' => 'تم تأكيد :confirmed عرض سعر، وتخطي :skipped.',
        ],
    ],

    'team-filter' => [
        'label' => 'فريق المبيعات: :team',
        'clear' => 'عرض الكل',
    ],
];
