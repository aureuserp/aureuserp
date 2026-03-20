<?php

return [
    'title'        => 'إرسال بالبريد الإلكتروني',
    'resend-title' => 'إعادة الإرسال بالبريد الإلكتروني',
    'quotation'    => 'عرض السعر',
    'quotations'   => 'عروض الأسعار',

    'modal' => [
        'heading' => 'إرسال عرض السعر بالبريد الإلكتروني',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'الشركاء',
            'subject'     => 'الموضوع',
            'description' => 'الوصف',
            'attachment'  => 'المرفق',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'لم يتم تحديد مستلمين',
                    'body'  => 'يرجى تحديد شريك واحد على الأقل لإرسال عروض الأسعار إليه.',
                ],

                'all_success' => [
                    'title' => 'تم إرسال عروض الأسعار!',
                    'body'  => 'تم تسليم :plural الخاصة بك بنجاح إلى: :recipients',
                ],

                'all_failed' => [
                    'title' => 'تعذر إرسال عروض الأسعار',
                    'body'  => 'واجهتنا مشاكل في إرسال عروض الأسعار: :failures',
                ],

                'partial_success' => [
                    'title'       => 'تم إرسال بعض عروض الأسعار',
                    'sent_part'   => 'تم التسليم بنجاح إلى: :recipients',
                    'failed_part' => 'تعذر التسليم إلى: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
