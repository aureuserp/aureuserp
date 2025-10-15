<?php

return [
    'navigation' => [
        'title' => 'Sales',
    ],

    'navigation-group' => [
        'title' => 'Dashboard',
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
            'total-revenue' => 'Total Revenue',
            'avg-revenue'   => 'Avg. Revenue',

            'descriptions' => [
                'quotation' => 'Quotations sent to customers',
                'order'     => 'Orders confirmed by customers',
                'draft'     => 'Draft quotations',
                'cancel'    => 'Orders cancelled by customers',
                'total-revenue' => 'Total revenue from orders',
                'avg-revenue'   => 'Average revenue from orders',
            ],
        ],

        'sales-chart' => [
            'heading'          => 'Sales Chart',
            'confirmed-orders' => 'Confirmed Orders',
            'draft-orders'     => 'Draft Orders',
            'sent-orders'      => 'Sent Quotations',
            'cancelled-orders' => 'Cancelled Orders',
        ],

        'revenue-chart' => [
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

        'top-customers' => [
            'heading' => 'Top Customers',
            'column'  => [
                'customer'      => 'Customer',
                'total_orders'  => 'Total Orders',
                'total_revenue' => 'Total Revenue',
            ],
        ],

        'top-products' => [
            'heading' => 'Top Products',
            'column'  => [
                'product'       => 'Product',
                'total_orders'  => 'Total Orders',
                'total_revenue' => 'Total Revenue',
            ],
        ],

        'top-sales-teams' => [
            'heading' => 'Top Sales Teams',
            'column'  => [
                'sales_team'    => 'Sales Team',
                'total_orders'  => 'Total Orders',
                'total_revenue' => 'Revenue',
            ],
        ],

        'top-sales-countries' => [
            'heading' => 'Top Sales Countries',
            'column'  => [
                'country'       => 'Country',
                'total_orders'  => 'Total Orders',
                'total_revenue' => 'Total Revenue',
            ],
        ],
    ],
];
