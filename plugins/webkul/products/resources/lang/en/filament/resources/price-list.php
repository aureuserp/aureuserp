<?php

return [
    'navigation' => [
        'title' => 'Price Lists',
        'group' => 'Products',
    ],

    'header-actions' => [
        'create' => [
            'label' => 'New Price List',
        ],
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'General Information',

                'fields' => [
                    'name'     => 'Price List Name',
                    'currency' => 'Currency',
                    'company'  => 'Company',
                    'status'   => 'Active',
                ],
            ],

            'rules' => [
                'title'        => 'Price Rules',
                'description'  => 'The first rule that matches a product decides its price. More specific rules win over broader ones.',
                'add-rule'     => 'Add Rule',
                'all-products' => 'All Products',

                'actions' => [
                    'edit' => 'Edit Rule',
                ],

                'columns' => [
                    'apply-to'     => 'Apply On',
                    'target'       => 'Applies To',
                    'min-quantity' => 'Min. Quantity',
                    'type'         => 'Compute Price',
                    'price'        => 'Price',
                    'period'       => 'Period',
                ],

                'formula' => [
                    'discount'     => 'discount',
                    'markup'       => 'markup',
                    'rule-tip'     => ':base with a :discount % :type and :surcharge extra fee'."\n".'Example: :amount * :factor + :surcharge → :total',
                    'rounding-tip' => 'Tip: want to round at 9.99? Round off to 10.00 and set an extra fee at -0.01.',
                ],

                'fields' => [
                    'apply-to-product'      => 'Product',
                    'apply-to-category'     => 'Category',
                    'all-products'          => 'All Products',
                    'all-categories'        => 'All Categories',
                    'all-variants'          => 'All Variants',
                    'variant'               => 'Variant',
                    'on'                    => 'on',
                    'sales-price'           => 'Sales Price',
                    'apply-to'              => 'Apply To',
                    'product'               => 'Product',
                    'category'              => 'Product Category',
                    'min-quantity'          => 'Min Qty',
                    'type'                  => 'Price Type',
                    'fixed-price'           => 'Fixed Price',
                    'percent-price'         => 'Discount',
                    'percent-price-helper'  => 'Use a negative value to apply a mark-up.',
                    'base'                  => 'Based price',
                    'base-price-list'       => 'Other Price List',
                    'price-discount'        => 'Discount',
                    'price-discount-helper' => 'Use a negative value to apply a mark-up.',
                    'price-markup'          => 'Markup',
                    'price-round'           => 'Round off to',
                    'price-round-helper'    => 'Rounds the price to a multiple of this value, applied after the discount and before the extra fee.',
                    'price-surcharge'       => 'Extra Fee',
                    'price-min-margin'      => 'Min. Margin',
                    'price-max-margin'      => 'Max. Margin',
                    'starts-at'             => 'Validity Period',
                    'ends-at'               => 'End Date',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'     => 'Price List',
            'currency' => 'Currency',
            'company'  => 'Company',
            'rules'    => 'Rules',
            'status'   => 'Active',
        ],

        'filters' => [
            'status'   => 'Active',
            'currency' => 'Currency',
        ],
    ],
];
