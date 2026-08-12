<?php

return [
    'notification' => [
        'title' => 'Purchase Agreement updated',
        'body'  => 'The purchase agreement has been updated successfully.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Confirm',

            'notification' => [
                'unable' => [
                    'title' => 'Unable to confirm purchase agreement',
                    'body'  => 'Add at least one product line before confirming this purchase agreement.',
                ],
            ],
        ],

        'close' => [
            'label'        => 'Close',
            'notification' => [
                'warning' => [
                    'title' => 'Unable to close purchase agreement',
                    'body'  => 'You cannot close this purchase agreement because some related RFQs are not in Done or Canceled status.',
                ],
            ],
        ],

        'cancel' => [
            'label' => 'Cancel',
        ],

        'print' => [
            'label' => 'Print',
        ],

        'delete' => [
            'notification' => [
                'title' => 'Purchase Agreement deleted',
                'body'  => 'The purchase agreement has been deleted successfully.',
            ],
        ],
    ],
];
