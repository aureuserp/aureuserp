<?php

return [
    'title'        => 'Asset Warehouse',
    'plural-title' => 'Asset Warehouses',

    'navigation' => [
        'title' => 'Warehouses',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Warehouse Details',
                'fields' => [
                    'name'             => 'Name',
                    'code'             => 'Code',
                    'code-help'        => 'Optional internal code, unique within the company.',
                    'note'             => 'Address / Location & Notes',
                    'note-placeholder' => 'Location or storage notes.',
                ],
            ],

            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'company'        => 'Company',
                    'custodian'      => 'Warehouse Keeper',
                    'custodian-help' => 'Employee in charge of stored assets in this warehouse.',
                    'is-active'      => 'Active',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'code'         => 'Code',
            'custodian'    => 'Warehouse Keeper',
            'assets-count' => 'Stored Assets',
            'total-cost'   => 'Total Valuation',
            'is-active'    => 'Active',
            'company'      => 'Company',
            'creator'      => 'Created By',
            'deleted-at'   => 'Archived At',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'filters' => [
            'custodian' => 'Warehouse Keeper',
            'is-active' => 'Active',
            'company'   => 'Company',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Warehouse updated',
                    'body'  => 'The warehouse has been updated successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Warehouse could not be deleted',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Warehouse Details',
                'entries' => [
                    'name'      => 'Name',
                    'code'      => 'Code',
                    'custodian' => 'Warehouse Keeper',
                    'company'   => 'Company',
                    'is-active' => 'Active',
                    'note'      => 'Address / Location & Notes',
                ],
            ],

            'audit' => [
                'title'   => 'Audit',
                'entries' => [
                    'creator'    => 'Created By',
                    'created-at' => 'Created At',
                    'updated-at' => 'Updated At',
                ],
            ],
        ],
    ],

    'relation-manager' => [
        'warehouse-assets' => [
            'title'   => 'Stored Assets',
            'columns' => [
                'asset-tag'     => 'Asset Tag',
                'asset-name'    => 'Asset Name',
                'product'       => 'Product',
                'model'         => 'Model',
                'serial-no'     => 'Serial No.',
                'category'      => 'Category',
                'condition'     => 'Condition',
                'cost'          => 'Cost',
                'purchase-date' => 'Purchase Date',
            ],
            'actions' => [
                'issue' => 'Issue Asset',
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'New Asset Warehouse',
                    'notification' => [
                        'title' => 'Warehouse created',
                        'body'  => 'The warehouse has been created successfully.',
                    ],
                ],
            ],
            'views' => [
                'all'      => 'All Warehouses',
                'archived' => 'Archived Warehouses',
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Warehouse created',
                'body'  => 'The warehouse has been created successfully.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Warehouse updated',
                'body'  => 'The warehouse has been updated successfully.',
            ],
        ],
    ],
];
