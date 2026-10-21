<?php

return [
    'label'          => 'إرجاع المنتجات',
    'refunded-label' => 'تم الاسترداد',

    'form' => [
        'fields' => [
            'payment-method' => 'طريقة دفع الاسترداد',
            'lines'          => 'البنود المراد استردادها',
            'selected'       => 'استرداد',
            'product'        => 'المنتج',
            'refundable'     => 'قابل للاسترداد',
            'quantity'       => 'الكمية',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'تم إنشاء الاسترداد',
            'body'  => 'تم إنشاء طلب استرداد للبنود المحددة.',
        ],
    ],
];
