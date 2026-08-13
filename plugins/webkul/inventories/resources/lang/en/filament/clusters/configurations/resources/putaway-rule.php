<?php

return [
    'navigation' => [
        'title' => 'Putaway Rules',
        'group' => 'Warehouse Management',
    ],

    'form' => [
        'fields' => [
            'in-location'          => 'When Product Arrives In',
            'product'              => 'Product',
            'product-placeholder'  => 'All Products',
            'category'             => 'Product Category',
            'category-placeholder' => 'All Categories',
            'storage-category'     => 'Storage Category',
            'out-location'         => 'Store To',
            'sub-location'         => 'Sub Location',
            'company'              => 'Company',
        ],
    ],

    'table' => [
        'columns' => [
            'in-location'      => 'When Product Arrives In',
            'product'          => 'Product',
            'category'         => 'Product Category',
            'storage-category' => 'Storage Category',
            'out-location'     => 'Store To',
            'sub-location'     => 'Sub Location',
            'company'          => 'Company',
            'deleted-at'       => 'Deleted At',
            'created-at'       => 'Created At',
            'updated-at'       => 'Updated At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Putaway rule updated',
                    'body'  => 'The putaway rule has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Putaway rule restored',
                    'body'  => 'The putaway rule has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Putaway rule deleted',
                    'body'  => 'The putaway rule has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Putaway rule could not be deleted',
                        'body'  => 'The putaway rule cannot be permanently deleted because it is referenced by other records.',
                    ],

                    'success' => [
                        'title' => 'Putaway rule permanently deleted',
                        'body'  => 'The putaway rule has been permanently deleted successfully.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Putaway rules restored',
                    'body'  => 'The putaway rules have been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Putaway rules deleted',
                    'body'  => 'The putaway rules have been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Putaway rules could not be deleted',
                        'body'  => 'Some putaway rules cannot be permanently deleted because they are referenced by other records.',
                    ],

                    'success' => [
                        'title' => 'Putaway rules permanently deleted',
                        'body'  => 'The putaway rules have been permanently deleted successfully.',
                    ],
                ],
            ],
        ],
    ],
];
