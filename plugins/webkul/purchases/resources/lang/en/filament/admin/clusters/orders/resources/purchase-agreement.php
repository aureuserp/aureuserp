<?php

return [
    'navigation' => [
        'title' => 'Purchase Agreements',
        'group' => 'Purchase',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'vendor'                => 'Vendor',
                    'valid-from'            => 'Valid From',
                    'valid-to'              => 'Valid Until',
                    'buyer'                 => 'Buyer',
                    'reference'             => 'Reference',
                    'reference-placeholder' => 'eg. PO/123',
                    'agreement-type'        => 'Agreement Type',
                    'company'               => 'Company',
                    'currency'              => 'Currency',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Products',

                'columns' => [
                    'product'    => 'Product',
                    'quantity'   => 'Quantity',
                    'ordered'    => 'Ordered',
                    'uom'        => 'Unit of Measure',
                    'unit-price' => 'Unit Price',
                ],

                'fields' => [
                    'product'    => 'Product',
                    'quantity'   => 'Quantity',
                    'ordered'    => 'Ordered',
                    'uom'        => 'Unit of Measure',
                    'unit-price' => 'Unit Price',
                ],
            ],

            'additional' => [
                'title' => 'Additional Information',
            ],

            'terms' => [
                'title' => 'Terms and Conditions',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement'      => 'Agreement',
            'vendor'         => 'Vendor',
            'agreement-type' => 'Agreement Type',
            'buyer'          => 'Buyer',
            'company'        => 'Company',
            'valid-from'     => 'Valid From',
            'valid-to'       => 'Valid Until',
            'reference'      => 'Reference',
            'status'         => 'Status',
        ],

        'groups' => [
            'agreement-type' => 'Agreement Type',
            'vendor'         => 'Vendor',
            'state'          => 'State',
            'created-at'     => 'Created At',
            'updated-at'     => 'Updated At',
        ],

        'filters' => [
            'agreement'      => 'Agreement',
            'vendor'         => 'Vendor',
            'agreement-type' => 'Agreement Type',
            'buyer'          => 'Buyer',
            'company'        => 'Company',
            'valid-from'     => 'Valid From',
            'valid-to'       => 'Valid Until',
            'reference'      => 'Reference',
            'status'         => 'Status',
            'created-at'     => 'Created At',
            'updated-at'     => 'Updated At',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Purchase Agreement deleted',
                    'body'  => 'The purchase agreement has been deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Purchase Agreement restored',
                    'body'  => 'The purchase agreement has been restored successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Purchase Agreement permanently deleted',
                        'body'  => 'The purchase agreement has been permanently deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Purchase Agreement could not be deleted',
                        'body'  => 'The The purchase agreement cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Purchase Agreements deleted',
                    'body'  => 'The purchase agreements has been deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Purchase Agreements restored',
                    'body'  => 'The purchase agreements has been restored successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Purchase Agreements permanently deleted',
                        'body'  => 'The purchase agreements has been permanently deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Purchase Agreements could not be deleted',
                        'body'  => 'The purchase agreements cannot be deleted because they are currently in use.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'vendor'                => 'Vendor',
                    'valid-from'            => 'Valid From',
                    'valid-to'              => 'Valid Until',
                    'buyer'                 => 'Buyer',
                    'reference'             => 'Reference',
                    'reference-placeholder' => 'eg. PO/123',
                    'agreement-type'        => 'Agreement Type',
                    'company'               => 'Company',
                    'currency'              => 'Currency',
                ],
            ],

            'metadata' => [
                'title' => 'Metadata',

                'entries' => [
                    'created-at' => 'Created At',
                    'created-by' => 'Created By',
                    'updated-at' => 'Updated At',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Products',

                'entries' => [
                    'product'    => 'Product',
                    'quantity'   => 'Quantity',
                    'ordered'    => 'Ordered',
                    'uom'        => 'Unit of Measure',
                    'unit-price' => 'Unit Price',
                ],
            ],

            'additional' => [
                'title' => 'Additional Information',
            ],

            'terms' => [
                'title' => 'Terms and Conditions',
            ],
        ],
    ],
];
