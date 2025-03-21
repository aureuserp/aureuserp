<?php

return [
    'notification' => [
        'title' => 'Allocation updated',
        'body'  => 'The allocation has been updated successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Allocation deleted',
                'body'  => 'The allocation has been deleted successfully.',
            ],
        ],
        'approved' => [
            'title' => 'Approved',

            'notification' => [
                'title' => 'Allocation approved',
                'body'  => 'The allocation has been approved successfully.',
            ],
        ],
        'refuse' => [
            'title' => 'Refuse',

            'notification' => [
                'title' => 'Allocation refused',
                'body'  => 'The allocation has been refused successfully.',
            ],
        ],
        'mark-as-ready-to-confirm' => [
            'title' => 'Mark as Ready to Confirm',

            'notification' => [
                'title' => 'Marked as ready to confirm',
                'body'  => 'The allocation has been marked as ready to confirm successfully.',
            ],
        ],
    ],
];
