<?php

return [
    'title' => 'إنشاء فاتورة',

    'modal' => [
        'heading' => 'إنشاء فاتورة',
    ],

    'notification' => [
        'invoice-created' => [
            'title' => 'تم إنشاء الفاتورة',
            'body'  => 'تم إنشاء الفاتورة بنجاح.',
        ],

        'no-invoiceable-lines' => [
            'title' => 'لا توجد بنود للفوترة',
            'body'  => 'لا يوجد بند قابل للفوترة، يرجى التأكد من استلام الكمية.',
        ],
    ],

    'form' => [
        'fields' => [
            'create-invoice' => 'إنشاء فاتورة',
        ],
    ],
];
