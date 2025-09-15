<?php

return [
    'navigation' => [
        'title' => 'Categories',
        'group' => 'Blog',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Name',
            'name-placeholder' => 'Category title ...',
            'sub-title'        => 'Sub Title',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'name',
            'sub-title'  => 'Sub Title',
            'posts'      => 'Posts',
            'created-at' => 'Created At',
        ],

        'filters' => [
            'is-published' => 'Is Published',
            'author'       => 'Author',
            'creator'      => 'Created By',
            'category'     => 'Category',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Category updated',
                    'body'  => 'The category has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Category restored',
                    'body'  => 'The category has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Category deleted',
                    'body'  => 'The category has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Category force deleted',
                        'body'  => 'The Category has been force deleted successfully.',
                    ],
                    'error' => [
                        'title' => 'Category  could not be deleted',
                        'body'  => 'The Category  cannot be deleted because it is currently in use.',
                    ],
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'Cannot delete category',
                    'body' => 'You cannot delete this category as it is associated with some posts.',
                ],

                'exception' => 'You cannot force delete this category as it is associated with some posts.'
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Categories restored',
                    'body'  => 'The categories has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Categories deleted',
                    'body'  => 'The categories has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Categories force deleted',
                    'body'  => 'The categories has been force deleted successfully.',
                ],
            ],
            
            'force-delete-error' => [
                'notification' => [
                    'title' => 'Cannot delete category',
                    'body' => 'You cannot delete this category as it is associated with some posts.',
                ],
            ],
        ],
    ],

    'infolist' => [
    ],
];
