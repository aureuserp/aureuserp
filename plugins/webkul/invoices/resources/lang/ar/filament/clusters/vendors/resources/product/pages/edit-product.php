<?php

return [
    'notification' => [
        'title' => 'تم تحديث المنتج',
        'body'  => 'تم تحديث المنتج بنجاح.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'تحديث الكمية',
            'modal-heading'             => 'تحديث كمية المنتج',
            'modal-submit-action-label' => 'تحديث',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'الكمية المتاحة',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'تم حذف المنتج',
                'body'  => 'تم حذف المنتج بنجاح.',
            ],
        ],
    ],
];
