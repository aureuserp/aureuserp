<?php

return [

    'navigation' => [
        'title' => 'الجلسات',
        'group' => 'نقطة البيع',
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'جلسة',
                'entries' => [
                    'session'          => 'جلسة',
                    'opened-by'        => 'فتحها',
                    'point-of-sale'    => 'نقطة البيع',
                    'opening-date'     => 'تاريخ الافتتاح',
                    'starting-balance' => 'الرصيد الابتدائي',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                  => 'معرّف الجلسة',
            'config'                => 'نقطة البيع',
            'user'                  => 'فتحها',
            'started-at'            => 'تاريخ الافتتاح',
            'stopped-at'            => 'تاريخ الإغلاق',
            'cash-balance-start'    => 'الرصيد الابتدائي',
            'cash-balance-end-real' => 'الرصيد الختامي',
            'cash-balance-end'      => 'الرصيد الختامي النظري',
            'order-count'           => 'الطلبات',
            'total-payments-amount' => 'الدفعات',
            'cash-difference'       => 'الفرق',
            'is-rescue'             => 'إنقاذ',
            'has-failed-operations' => 'العمليات الفاشلة',
            'state'                 => 'الحالة',
            'company'               => 'الشركة',
        ],

        'groups' => [
            'config'     => 'نقطة البيع',
            'state'      => 'الحالة',
            'started-at' => 'فُتحت في',
        ],

        'filters' => [
            'state'  => 'الحالة',
            'config' => 'نقطة البيع',
        ],
    ],
];
