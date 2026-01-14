<?php

return [
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

        'tabs' => [
            'due-terms' => [
                'title' => 'Due Terms',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value'                  => 'Value',
                            'due'                    => 'Due',
                            'delay-type'             => 'Delay Type',
                            'days-on-the-next-month' => 'Days on the next month',
                            'days'                   => 'Days',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term' => 'Payment Term',
            'company'      => 'Company',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'groups' => [
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
                    'success' => [
                        'title' => 'Payment Term force deleted',
                        'body'  => 'The payment term has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Payment Term force deletion failed',
                        'body'  => 'The payment term could not be force deleted because it is associated journal entries.',
                    ],
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
                    'success' => [
                        'title' => 'Payment Terms force deleted',
                        'body'  => 'The payment Terms has been force deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Payment Terms force deletion failed',
                        'body'  => 'The payment terms could not be force deleted because they have associated journal entries.',
                    ],
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

        'tabs' => [
            'due-terms' => [
                'title' => 'Due Terms',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value'                  => 'Value',
                            'due'                    => 'Due',
                            'delay-type'             => 'Delay Type',
                            'days-on-the-next-month' => 'Days on the next month',
                            'days'                   => 'Days',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
