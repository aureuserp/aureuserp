<?php

return [
    'title' => 'Quotation Template',

    'navigation' => [
        'title'  => 'Quotation Template',
        'group'  => 'Sales Orders',
    ],

    'form' => [
        'tabs' => [
            'products' => [
                'title'  => 'Products',
                'fields' => [
                    'products'     => 'Products',
                    'name'         => 'Name',
                    'quantity'     => 'Quantity',
                ],
            ],

            'terms-and-conditions' => [
                'title'  => 'Terms & Conditions',
                'fields' => [
                    'note-placeholder' => 'Write your terms and conditions for the quotations.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'General Information',

                'fields' => [
                    'name'               => 'Name',
                    'quotation-validity' => 'Quotation Validity',
                    'sale-journal'       => 'Sale Journal',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'Signature & Payments',

                'fields' => [
                    'online-signature'      => 'Online Signature',
                    'online-payment'        => 'Online Payment',
                    'prepayment-percentage' => 'Prepayment Percentage',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by'            => 'Created by',
            'company'               => 'Company',
            'name'                  => 'Name',
            'number-of-days'        => 'Number of days',
            'journal'               => 'Sale Journal',
            'signature-required'    => 'Signature Required',
            'payment-required'      => 'Payment Required',
            'prepayment-percentage' => 'Prepayment Percentage',
        ],
        'groups'  => [
            'company' => 'Company',
            'name'    => 'Name',
            'journal' => 'Journal',
        ],
        'filters' => [
            'created-by' => 'Created By',
            'company'    => 'Company',
            'name'       => 'Name',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Quotation template deleted',
                    'body'  => 'The quotation template has been deleted successfully.',
                ],
            ],

        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Quotation template deleted',
                    'body'  => 'The quotation template has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'products' => [
                'title' => 'Products',
            ],
            'terms-and-conditions' => [
                'title' => 'Terms & Conditions',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'General Information',
            ],
            'signature_and_payment' => [
                'title' => 'Signature & Payment',
            ],
        ],
        'entries' => [
            'product'               => 'Product',
            'description'           => 'Description',
            'quantity'              => 'Quantity',
            'unit-price'            => 'Unit Price',
            'section-name'          => 'Section Name',
            'note-title'            => 'Note Title',
            'name'                  => 'Template Name',
            'quotation-validity'    => 'Quotation Validity',
            'sale-journal'          => 'Sale Journal',
            'online-signature'      => 'Online Signature',
            'online-payment'        => 'Online Payment',
            'prepayment-percentage' => 'Prepayment Percentage',
        ],
    ],
];
