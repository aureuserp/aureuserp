<?php

return [
    'notification' => [
        'title' => 'Attribute updated',
        'body'  => 'The attribute has been updated successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Attribute deleted',
                    'body'  => 'The attribute has been deleted successfully.',
                ],

                'error' => [
                    'title' => 'Attribute could not be deleted',
                    'body'  => 'The attribute cannot be deleted because it is currently in use.',
                ],
            ],
        ],
    ],
];
