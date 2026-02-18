<?php

return [
    'title' => 'Partners',

    'navigation' => [
        'title' => 'Partners',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Sales',

                        'fields' => [
                            'sales-person'   => 'Sales Person',
                            'payment-terms'  => 'Payment Terms',
                            'payment-method' => 'Payment Method',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Purchase',

                        'fields' => [
                            'payment-terms'  => 'Payment Terms',
                            'payment-method' => 'Payment Method',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Fiscal Information',

                        'fields' => [
                            'fiscal-position'    => 'Fiscal Position',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Invoicing',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Customer Invoices',

                        'fields' => [
                            'invoice-sending-method'   => 'Invoice Sending Method',
                            'invoice-edi-format-store' => 'eInvoice Format',
                            'peppol-eas'               => 'Peppol Address',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Accounting Entries',

                        'fields' => [
                            'account-receivable' => 'Account Receivable',
                            'account-payable'    => 'Account Payable',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automation',

                        'fields' => [
                            'auto-post-bills' => 'Auto Post Bills',
                            'ignore-abnormal-invoice-amount' => 'Ignore Abnormal Invoice Amount',
                            'ignore-abnormal-invoice-date' => 'Ignore Abnormal Invoice Date',
                        ],
                    ]
                ],
            ],

            'internal-notes' => [
                'title' => 'Internal Notes',
            ],
        ],
    ],

    'infolist' => [
        
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Sales',

                        'entries' => [
                            'sales-person'   => 'Sales Person',
                            'payment-terms'  => 'Payment Terms',
                            'payment-method' => 'Payment Method',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Purchase',

                        'entries' => [
                            'payment-terms'  => 'Payment Terms',
                            'payment-method' => 'Payment Method',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Fiscal Information',

                        'entries' => [
                            'fiscal-position'    => 'Fiscal Position',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Invoicing',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Customer Invoices',

                        'entries' => [
                            'invoice-sending-method'   => 'Invoice Sending Method',
                            'invoice-edi-format-store' => 'eInvoice Format',
                            'peppol-eas'               => 'Peppol Address',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Accounting Entries',

                        'entries' => [
                            'account-receivable' => 'Account Receivable',
                            'account-payable'    => 'Account Payable',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Automation',

                        'entries' => [
                            'auto-post-bills' => 'Auto Post Bills',
                            'ignore-abnormal-invoice-amount' => 'Ignore Abnormal Invoice Amount',
                            'ignore-abnormal-invoice-date' => 'Ignore Abnormal Invoice Date',
                        ],
                    ]
                ],
            ],

            'internal-notes' => [
                'title' => 'Internal Notes',
            ],
        ],
    ],
];
