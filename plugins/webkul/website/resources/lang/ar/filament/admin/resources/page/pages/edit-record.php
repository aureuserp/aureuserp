<?php

return [
    'notification' => [
        'title' => 'Page updated',
        'body'  => 'The page has been updated successfully.',
    ],

    'header-actions' => [
        'draft' => [
            'label' => 'Set as Draft',

            'notification' => [
                'title' => 'Page set as draft',
                'body'  => 'The page has been set as draft successfully.',
            ],
        ],

        'publish' => [
            'label' => 'Publish',

            'notification' => [
                'title' => 'Page published',
                'body'  => 'The page has been published successfully.',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Page deleted',
                'body'  => 'The page has been deleted successfully.',
            ],
        ],
    ],
];
