<?php

return [
    'label'             => 'تحقق',
    'modal-heading'     => 'إنشاء طلب متأخر؟',
    'modal-description' => 'قم بإنشاء طلب متأخر إذا كانت المنتجات المتبقية ستتم معالجتها لاحقاً. إذا لم يكن كذلك، لا تقم بإنشاء طلب متأخر.',

    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'بدون طلب متأخر',
        ],
    ],

    'notification' => [
        'error' => [
            'title' => 'فشل التحقق',
        ],

        'warning' => [
            'lines-missing' => [
                'title' => 'لا توجد كميات محجوزة',
                'body'  => 'لا توجد كميات محجوزة للتحويل.',
            ],

            'no-quantities-reserved' => [
                'title' => 'No quantities are reserved',
                'body'  => 'No quantities are reserved for the transfer.',
            ],

            'lot-missing' => [
                'title' => 'أدخل الدفعة/الرقم التسلسلي',
                'body'  => 'تحتاج إلى إدخال رقم الدفعة/التسلسلي للمنتجات  :products',
            ],

            'serial-qty' => [
                'title' => 'الرقم التسلسلي مستخدم بالفعل',
                'body'  => 'تم تعيين الرقم التسلسلي بالفعل لمنتج آخر.',
            ],

            'partial-package' => [
                'title' => 'لا يمكن نقل نفس محتوى الطرد',
                'body'  => 'لا يمكنك نقل نفس محتويات الطرد أكثر من مرة في تحويل واحد أو تقسيم الطرد بين موقعين.',
            ],
        ],
    ],
];
