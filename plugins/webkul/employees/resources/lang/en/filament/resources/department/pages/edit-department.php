<?php

return [
    'update' => [
        'success' => [
            'notification' => [
                'title' => 'Department updated',
                'body'  => 'The department has been updated successfully.',
            ],
        ],

        'error' => [
            'notification' => [
                'title' => 'Department update failed',
                'body'  => 'There was an error updating the department.',
            ],
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Department deleted',
                'body'  => 'The department has been deleted successfully.',
            ],
        ],
    ],
];
