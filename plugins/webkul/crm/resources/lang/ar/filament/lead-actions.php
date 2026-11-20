<?php

return [
    'mark-lost' => [
        'label'        => 'تحديد كمفقود',
        'notification' => 'تم تحديد :count سجل كمفقود.',
    ],

    'send-email' => [
        'label'        => 'إرسال بريد إلكتروني',
        'short-label'  => 'بريد',
        'submit'       => 'إرسال',

        'form' => [
            'recipient' => 'إلى',
            'subject'   => 'الموضوع',
            'body'      => 'الرسالة',
        ],

        'sent'         => 'تم إرسال البريد إلى :email.',
        'failed'       => 'تعذر إرسال البريد الإلكتروني.',
        'notification' => 'تم إرسال :sent رسالة، وتخطي :skipped سجل (بلا بريد إلكتروني أو بلا صلاحية).',
    ],

    'send-sms' => [
        'label'        => 'إرسال رسالة نصية',
        'short-label'  => 'رسالة نصية',
        'submit'       => 'إرسال الرسالة',

        'form' => [
            'recipient' => 'المستلم',
            'message'   => 'الرسالة',
        ],

        'log-subject'  => 'رسالة نصية إلى :number',
        'sent'         => 'تم إرسال الرسالة النصية إلى :number.',
        'failed'       => 'تعذر إرسال الرسالة النصية.',
        'notification' => 'تم إرسال :sent رسالة نصية، وتخطي :skipped سجل (بلا رقم صالح أو بلا صلاحية).',
    ],
];
