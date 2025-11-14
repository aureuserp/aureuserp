<?php

return [
    'title' => 'Vendors',

    'navigation' => [
        'title' => 'Vendors',
    ],

    'form' => [
        'fields' => [
            'sales-person'       => 'Sales Person',
            'payment-terms'      => 'Payment Terms',
            'payment-method'     => 'Payment Method',
            'fiscal-position'    => 'Fiscal Position',
            'purchase'           => 'Purchase',
            'fiscal-information' => 'Fiscal Information',
        ],
        'tabs' => [
            'invoicing' => [
                'title'  => 'Invoicing',
                'fields' => [
                    'customer-invoices'              => 'Customer Invoices',
                    'invoice-sending-method'         => 'Invoice Sending Method',
                    'invoice-edi-format-store'       => 'eInvoice Format',
                    'peppol-eas'                     => 'Peppol Address',
                    'endpoint'                       => 'Endpoint',
                    'auto-post-bills'                => 'Auto Post Bills',
                    'automation'                     => 'Automation',
                    'ignore-abnormal-invoice-amount' => 'Ignore Abnormal Invoice Amount',
                    'ignore-abnormal-invoice-date'   => 'Ignore Abnormal Invoice Date',
                ],
            ],
            'internal-notes' => [
                'title' => 'Internal Notes',
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sales-person'       => 'Sales Person',
            'payment-terms'      => 'Payment Terms',
            'payment-method'     => 'Payment Method',
            'fiscal-position'    => 'Fiscal Position',
            'purchase'           => 'Purchase',
            'fiscal-information' => 'Fiscal Information',
        ],
        'tabs' => [
            'invoicing' => [
                'title'   => 'Invoicing',
                'entries' => [
                    'customer-invoices'              => 'Customer Invoices',
                    'invoice-sending-method'         => 'Invoice Sending Method',
                    'invoice-edi-format-store'       => 'eInvoice Format',
                    'peppol-eas'                     => 'Peppol Address',
                    'endpoint'                       => 'Endpoint',
                    'auto-post-bills'                => 'Auto Post Bills',
                    'automation'                     => 'Automation',
                    'ignore-abnormal-invoice-amount' => 'Ignore Abnormal Invoice Amount',
                    'ignore-abnormal-invoice-date'   => 'Ignore Abnormal Invoice Date',
                ],
            ],
            'internal-notes' => [
                'title' => 'Internal Notes',
            ],
        ],
    ],
];
