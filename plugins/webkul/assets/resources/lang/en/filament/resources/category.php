<?php

return [
    'title'        => 'Asset Category',
    'plural-title' => 'Asset Categories',

    'navigation' => [
        'title' => 'Asset Categories',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Category Details',
                'fields' => [
                    'name'   => 'Category Name',
                    'parent' => 'Parent Category',
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

    'table' => [
        'columns' => [
            'name'           => 'Name',
            'full-name'      => 'Full Path',
            'parent'         => 'Parent Category',
            'products-count' => 'Products',
            'assets-count'   => 'Assets',
            'total-cost'     => 'Total Cost',
            'company'        => 'Company',
            'creator'        => 'Created By',
            'created-at'     => 'Created At',
            'updated-at'     => 'Updated At',
        ],

        'filters' => [
            'parent'  => 'Parent Category',
            'company' => 'Company',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Category updated',
                    'body'  => 'The category has been updated successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Category Details',
                'entries' => [
                    'name'           => 'Name',
                    'full-name'      => 'Full Path',
                    'parent'         => 'Parent Category',
                    'products-count' => 'Products',
                    'assets-count'   => 'Assets',
                    'total-cost'     => 'Total Cost',
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
        'category-products' => [
            'title' => 'Products in this Category',
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label'        => 'New Category',
                    'notification' => [
                        'title' => 'Category created',
                        'body'  => 'The category has been created successfully.',
                    ],
                ],
            ],
        ],
        'create' => [
            'notification' => [
                'title' => 'Category created',
                'body'  => 'The category has been created successfully.',
            ],
        ],
        'edit' => [
            'notification' => [
                'title' => 'Category updated',
                'body'  => 'The category has been updated successfully.',
            ],
        ],
    ],
];
