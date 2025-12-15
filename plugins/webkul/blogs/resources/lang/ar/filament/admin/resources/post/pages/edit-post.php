<?php

return [
    'notification' => [
        'title' => 'Post updated',
        'body'  => 'The post has been updated successfully.',
    ],

    'header-actions' => [
        'draft' => [
            'label' => 'Set as Draft',

            'notification' => [
                'title' => 'Post set as draft',
                'body'  => 'The post has been set as draft successfully.',
            ],
        ],

        'publish' => [
            'label' => 'Publish',

            'notification' => [
                'title' => 'Post published',
                'body'  => 'The post has been published successfully.',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Post deleted',
                'body'  => 'The post has been deleted successfully.',
            ],
        ],
    ],
];
