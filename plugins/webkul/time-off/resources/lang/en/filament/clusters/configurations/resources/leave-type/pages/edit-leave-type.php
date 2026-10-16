<?php

return [
    'notification' => [
        'title' => 'Leave Type updated',
        'body'  => 'The leave type has been updated successfully.',

        'allocation-locked' => [
            'title' => 'Allocation requirement locked',
            'body'  => 'The allocation requirement of a time off type cannot be changed once leaves of that type have been taken.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Leave Type deleted',
                'body'  => 'The leave type has been deleted successfully.',
            ],
        ],
    ],
];
