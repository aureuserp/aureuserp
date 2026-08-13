<?php

return [
    'title' => 'المواقع',

    'table' => [
        'columns' => [
            'product'          => 'المنتج',
            'location'         => 'الموقع',
            'storage-category' => 'فئة التخزين',
            'quantity'         => 'الكمية',
            'package'          => 'الطرد',
            'on-hand'          => 'الكمية المتاحة',
            'unit'             => 'الوحدة',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الكمية',
                    'body'  => 'تم حذف الكمية بنجاح.',
                ],
            ],
        ],
    ],
];
