<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Payment term updated',
            'body'  => 'The payment term has been updated successfully.',
        ],

        'validation-error' => [
            'title' => 'Validation Error',
            'body'  => 'The Due Term must have at least one percent line and the sum of the percent must be 100%.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Payment term deleted',
                'body'  => 'The payment term has been deleted successfully.',
            ],
        ],
    ],
];
