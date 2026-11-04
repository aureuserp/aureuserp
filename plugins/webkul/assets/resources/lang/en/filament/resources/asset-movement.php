<?php

return [
    'navigation' => [
        'title' => 'Movements',
    ],

    'label'        => 'Asset Movement',
    'plural-label' => 'Asset Movements',

    'table' => [
        'columns' => [
            'occurred-at'        => 'Date & Time',
            'asset-tag'          => 'Asset Tag',
            'asset-name'         => 'Asset Name',
            'type'               => 'Movement Type',
            'quantity'           => 'Quantity',
            'source-type'        => 'Source',
            'from'               => 'From',
            'to'                 => 'To',
            'condition'          => 'Condition',
            'performed-by'       => 'Performed By',
            'expected-return-at' => 'Expected Return',
            'note'               => 'Note',
            'attachments'        => 'Attachments',
        ],
        'groups' => [
            'date'         => 'Date',
            'type'         => 'Movement Type',
            'asset'        => 'Asset',
            'condition'    => 'Condition',
            'performed-by' => 'Performed By',
        ],
        'filters' => [
            'type'         => 'Movement Type',
            'product-type' => 'Product Type',
            'source-type'  => 'Source',
            'condition'    => 'Condition',
            'category'     => 'Category',
            'product'      => 'Product',
            'asset'        => 'Asset',
            'warehouse'    => 'Warehouse',
            'employee'     => 'Employee',
            'department'   => 'Department',
            'site'         => 'Site',
            'performed-by' => 'Performed By',
            'date-from'    => 'From Date',
            'date-until'   => 'Until Date',
        ],
        'actions' => [
            'view' => 'View Details',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Export',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'tabs' => [
                'all'           => 'All',
                'assets'        => 'Assets',
                'consumables'   => 'Consumables',
                'issues'        => 'Issues',
                'returns'       => 'Returns',
                'transfers'     => 'Transfers',
                'scrapped-lost' => 'Scrapped / Lost',
                'damaged'       => 'Damaged / Poor',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'details'     => 'Movement Information',
            'attachments' => 'Attachments & Documentation',
        ],
    ],
];
