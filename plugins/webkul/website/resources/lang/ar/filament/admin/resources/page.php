<?php

return [
    'navigation' => [
        'title' => 'Pages',
        'group' => 'Website',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'title'             => 'Title',
                    'title-placeholder' => 'Page title ...',
                    'slug'              => 'Slug',
                    'content'           => 'Content',
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
                    'is-header-visible' => 'Is Visible Header Menu',
                    'is-footer-visible' => 'Is Visible Footer Menu',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'             => 'Title',
            'slug'              => 'Slug',
            'creator'           => 'Created By',
            'is-published'      => 'Is Published',
            'is-header-visible' => 'Is Visible Header Menu',
            'is-footer-visible' => 'Is Visible Footer Menu',
            'created-at'        => 'Created At',
            'updated-at'        => 'Updated At',
        ],

        'groups' => [
            'created-at' => 'Created At',
        ],

        'filters' => [
            'is-published' => 'Is Published',
            'creator'      => 'Created By',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Page updated',
                    'body'  => 'The page has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Page restored',
                    'body'  => 'The page has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Page deleted',
                    'body'  => 'The page has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Page force deleted',
                    'body'  => 'The page has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Pages restored',
                    'body'  => 'The pages has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Pages deleted',
                    'body'  => 'The pages has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Pages force deleted',
                    'body'  => 'The pages has been force deleted successfully.',
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
                    'is-header-visible' => 'Is Visible Header Menu',
                    'is-footer-visible' => 'Is Visible Footer Menu',
                ],
            ],
        ],
    ],
];
