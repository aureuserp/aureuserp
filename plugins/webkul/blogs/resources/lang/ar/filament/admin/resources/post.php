<?php

return [
    'navigation' => [
        'title' => 'Blog Posts',
        'group' => 'Website',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'title'             => 'Title',
                    'sub-title'         => 'Sub Title',
                    'title-placeholder' => 'Post title ...',
                    'slug'              => 'Slug',
                    'content'           => 'Content',
                    'banner'            => 'Banner',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'fields' => [
                    'meta-title'       => 'Meta Title',
                    'meta-keywords'    => 'Meta Keywords',
                    'meta-description' => 'Meta Description',
                ],
            ],

            'settings' => [
                'title' => 'Settings',

                'fields' => [
                    'category'     => 'Category',
                    'tags'         => 'Tags',
                    'name'         => 'Name',
                    'color'        => 'Color',
                    'is-published' => 'Is Published',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Title',
            'slug'         => 'Slug',
            'author'       => 'Author',
            'category'     => 'Category',
            'creator'      => 'Created By',
            'is-published' => 'Is Published',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'groups' => [
            'category'   => 'Category',
            'author'     => 'Author',
            'created-at' => 'Created At',
        ],

        'filters' => [
            'is-published' => 'Is Published',
            'author'       => 'Author',
            'creator'      => 'Created By',
            'category'     => 'Category',
            'tags'         => 'Tags',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Post updated',
                    'body'  => 'The post has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Post restored',
                    'body'  => 'The post has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Post deleted',
                    'body'  => 'The post has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Post force deleted',
                    'body'  => 'The post has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Posts restored',
                    'body'  => 'The posts has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Posts deleted',
                    'body'  => 'The posts has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Posts force deleted',
                    'body'  => 'The posts has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'title'   => 'Title',
                    'slug'    => 'Slug',
                    'content' => 'Content',
                    'banner'  => 'Banner',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'entries' => [
                    'meta-title'       => 'Meta Title',
                    'meta-keywords'    => 'Meta Keywords',
                    'meta-description' => 'Meta Description',
                ],
            ],

            'record-information' => [
                'title' => 'Record Information',

                'entries' => [
                    'author'          => 'Author',
                    'created-by'      => 'Created By',
                    'published-at'    => 'Published At',
                    'last-updated-by' => 'Last Updated By',
                    'last-updated'    => 'Last Updated At',
                    'created-at'      => 'Created At',
                ],
            ],

            'settings' => [
                'title' => 'Settings',

                'entries' => [
                    'category'     => 'Category',
                    'tags'         => 'Tags',
                    'name'         => 'Name',
                    'color'        => 'Color',
                    'is-published' => 'Is Published',
                ],
            ],
        ],
    ],
];
