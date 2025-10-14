<?php

return [
    'navigation' => [
        'title' => 'Sales',
    ],

    'filters-form' => [
        'start-date'     => 'Start Date',
        'end-date'       => 'End Date',
        'salesperson'    => 'Sales Person',
        'country'        => 'Country',
        'product'        => 'Product',
        'customer'       => 'Customer',
        'category'       => 'Category',
        'salesteam'      => 'Sales Team',
    ],

    'widgets' => [
        'stats-overview' => [
            'heading'   => 'Sales Overview',
            'quotation' => 'Quotation',
            'order'     => 'Order',
            'draft'     => 'Draft Quotation',
            'cancel'    => 'Cancel Quotation',

            'descriptions' => [
                'quotation' => 'Quotations sent to customers',
                'order'     => 'Orders confirmed by customers',
                'draft'     => 'Draft quotations',
                'cancel'    => 'Orders cancelled by customers',
            ],
        ],

        'sales-chart' => [
            'heading'          => 'Sales Chart',
            'confirmed-orders' => 'Confirmed Orders',
            'draft-orders'     => 'Draft Orders',
            'sent-orders'      => 'Sent Orders',
            'cancelled-orders' => 'Cancelled Orders',
        ],

        'revenew-chart' => [
            'heading' => 'Revenue Chart',
            'label'   => 'Revenue',
        ],

        'yearly-comparison' => [
            'heading' => 'Yearly Sales Comparison',
            'label'   => 'Sales',
        ],

        'top-categories' => [
            'heading' => 'Top Categories',
            'column'  => [
                'category'              => 'Category',
                'category_full_name'    => 'Full Name',
                'product_count'         => 'Product Count',
            ],
        ],
    ],
];
