<?php

return [
    'navigation' => [
        'settings' => [
            'label' => 'Settings',
            'group' => 'Purchase',
        ],
    ],

    'documents' => [
        'purchase-order-title'  => 'Purchase Order #:name',
        'buyer'                 => 'Buyer',
        'order-reference'       => 'Order Reference',
        'order-deadline'        => 'Order Deadline',
        'expected-arrival'      => 'Expected Arrival',
        'description'           => 'Description',
        'product'               => 'Product',
        'quantity'              => 'Quantity',
        'unit'                  => 'Unit',
        'unit-price'            => 'Unit Price',
        'discount'              => 'Discount',
        'tax'                   => 'Tax',
        'taxes'                 => 'Taxes',
        'amount'                => 'Amount',
        'untaxed-amount'        => 'Untaxed Amount',
        'total'                 => 'Total',
        'quotation-valid-until' => 'Quotation Valid Until:',
        'payment-terms'         => 'Payment Terms:',
        'agreement-validity'    => 'Agreement Validity',
        'contact'               => 'Contact',
        'reference'             => 'Reference',
        'terms-and-conditions'  => 'Terms & Conditions:',
        'additional-terms'      => 'Additional Terms:',
    ],

    'api' => [
        'purchase-order' => [
            'currency-locked'   => 'The currency cannot be changed once the purchase order is confirmed.',
            'order-date-locked' => 'The order deadline cannot be changed once the purchase order is confirmed.',
        ],
    ],
];
