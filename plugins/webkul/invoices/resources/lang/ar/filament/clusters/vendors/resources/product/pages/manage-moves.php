<?php

return [
    'title' => 'وارد/صادر',

    'table' => [
        'columns' => [
            'date'                 => 'التاريخ',
            'reference'            => 'المرجع',
            'product'              => 'المنتج',
            'package'              => 'الطرد',
            'lot'                  => 'الدفعة / الأرقام التسلسلية',
            'source-location'      => 'موقع المصدر',
            'destination-location' => 'موقع الوجهة',
            'quantity'             => 'الكمية',
            'state'                => 'الحالة',
            'done-by'              => 'تم بواسطة',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الحركة',
                    'body'  => 'تم حذف الحركة بنجاح.',
                ],
            ],
        ],
    ],
];
