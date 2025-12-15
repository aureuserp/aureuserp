<?php

return [
    'table' => [
        'columns' => [
            'reference'         => 'Reference',
            'total-amount'      => 'Total Amount',
            'confirmation-date' => 'Confirmation Date',
            'status'            => 'Status',
        ],
    ],

    'infolist' => [
        'settings' => [
            'entries' => [
                'buyer' => 'Buyer',
            ],

            'actions' => [
                'accept' => [
                    'label' => 'Accept',

                    'notification' => [
                        'title' => 'Quotation Accepted',
                        'body'  => 'The RFQ has been acknowledged successfully.',
                    ],

                    'message' => [
                        'body' => 'The RFQ has been acknowledged by vendor.',
                    ],
                ],

                'decline' => [
                    'label' => 'Decline',

                    'notification' => [
                        'title' => 'Quotation Declined',
                        'body'  => 'The RFQ has been declined successfully.',
                    ],

                    'message' => [
                        'body' => 'The RFQ has been declined by vendor.',
                    ],
                ],

                'print' => [
                    'label' => 'Download/Print',
                ],
            ],
        ],

        'general' => [
            'entries' => [
                'purchase-order'        => 'Purchase Order #:id',
                'quotation'             => 'Request for Quotation #:id',
                'order-date'            => 'Order Date',
                'from'                  => 'From',
                'confirmation-date'     => 'Confirmation Date',
                'receipt-date'          => 'Receipt Date',
                'products'              => 'Products',
                'untaxed-amount'        => 'Untaxed Amount',
                'tax-amount'            => 'Tax Amount',
                'total'                 => 'Total',
                'communication-history' => 'Communication History',
            ],
        ],
    ],
];
