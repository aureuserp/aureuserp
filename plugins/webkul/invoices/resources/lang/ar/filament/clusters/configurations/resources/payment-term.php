<?php

return [
    'title' => 'Payment Terms',

    'navigation' => [
        'title' => 'Payment Terms',
        'group' => 'Invoicing',
    ],

    'global-search' => [
        'company-name' => 'Company Name',
        'payment-term' => 'Payment Term',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'Payment Term',
                'early-discount'       => 'Early Discount',
                'discount-days-prefix' => 'if paid within',
                'discount-days-suffix' => 'days',
                'reduced-tax'          => 'Reduced tax',
                'note'                 => 'Note',
                'status'               => 'Status',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term'       => 'Payment Term',
            'company'            => 'Company',
            'discount-days'      => 'Discount Days',
            'early-pay-discount' => 'Early Pay Discount',
            'status'             => 'Status',
            'early-discount'     => 'Early Discount',
            'display-on-invoice' => 'Display on Invoice',
            'created-by'         => 'Created By',
            'created-at'         => 'Created At',
            'updated-at'         => 'Updated At',
        ],

        'groups' => [
            'payment-term'        => 'Payment Term',
            'company-name'        => 'Company Name',
            'discount-days'       => 'Discount Days',
            'early-pay-discount'  => 'Early Pay Discount',
            'payment-term'        => 'Payment Term',
            'display-on-invoice'  => 'Display on Invoice',
            'early-discount'      => 'Early Discount',
            'discount-percentage' => 'Discount Percentage',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Payment Term restored',
                    'body'  => 'The payment term has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Payment Term deleted',
                    'body'  => 'The payment term has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Payment Term force deleted',
                    'body'  => 'The payment term has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Payment Terms restored',
                    'body'  => 'The payment Terms has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Payment Terms deleted',
                    'body'  => 'The payment Terms has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Payment Terms force deleted',
                    'body'  => 'The payment Terms has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'         => 'Payment Term',
                'early-discount'       => 'Early Discount',
                'discount-percentage'  => 'Discount Percentage',
                'discount-days-prefix' => 'if paid within',
                'discount-days-suffix' => 'days',
                'reduced-tax'          => 'Reduced tax',
                'note'                 => 'Note',
                'status'               => 'Status',
            ],
        ],
    ],
];
