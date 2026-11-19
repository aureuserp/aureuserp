<?php

return [
    'navigation' => [
        'title' => 'Products',
    ],

    'label'        => 'Product',
    'plural-label' => 'Products',

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General Information',
                'fields' => [
                    'name'                 => 'Product Name',
                    'category'             => 'Category',
                    'type'                 => 'Product Type',
                    'type-asset-hint'      => 'A tracked unit identified by serial number and custody holder.',
                    'type-consumable-hint' => 'A quantity batch issued in units without return tracking.',
                    'model'                => 'Model',
                    'cost'                 => 'Standard Cost',
                    'is-active'            => 'Active',
                    'note'                 => 'Description / Specifications',
                ],
            ],

            'settings' => [
                'title'  => 'Settings',
                'fields' => [
                    'company' => 'Company',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General Information',
                'entries' => [
                    'name'      => 'Product Name',
                    'category'  => 'Category',
                    'type'      => 'Product Type',
                    'model'     => 'Model',
                    'cost'      => 'Standard Cost',
                    'is-active' => 'Active',
                    'note'      => 'Description / Specifications',
                ],
            ],

            'settings' => [
                'title'   => 'Settings',
                'entries' => [
                    'company' => 'Company',
                ],
            ],

            'audit' => [
                'title'   => 'Audit Information',
                'entries' => [
                    'creator'    => 'Created By',
                    'created-at' => 'Created At',
                    'updated-at' => 'Updated At',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                    => 'Product Name',
            'category'                => 'Category',
            'type'                    => 'Product Type',
            'model'                   => 'Model',
            'cost'                    => 'Cost',
            'assets-count'            => 'Registered Assets',
            'available-assets-count'  => 'Available in Warehouse',
            'held-assets-count'       => 'Issued in Custody',
            'terminated-assets-count' => 'Scrapped / Lost',
            'total-cost'              => 'Total Valuation',
            'is-active'               => 'Active',
            'company'                 => 'Company',
            'deleted-at'              => 'Archived At',
            'created-at'              => 'Created At',
            'updated-at'              => 'Updated At',
        ],

        'groups' => [
            'category' => 'Category',
        ],

        'filters' => [
            'category'  => 'Category',
            'type'      => 'Product Type',
            'is-active' => 'Active',
            'company'   => 'Company',
        ],
    ],

    'views' => [
        'active'   => 'Active Products',
        'archived' => 'Archived Products',
    ],

    'relation-manager' => [
        'assets' => [
            'title'   => 'Registered Asset Units',
            'actions' => [
                'open' => 'View Asset',
            ],
        ],
    ],
];
