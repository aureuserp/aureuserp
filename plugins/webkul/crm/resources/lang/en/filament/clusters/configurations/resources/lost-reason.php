<?php

return [
    'model-label'        => 'Lost Reason',
    'plural-model-label' => 'Lost Reasons',

    'navigation' => [
        'title' => 'Lost Reasons',
        'group' => 'Pipeline',
    ],

    'form' => [
        'fields' => [
            'name' => 'Description',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Description',
            'created-by' => 'Created By',
            'created-at' => 'Created At',
            'updated-at' => 'Updated At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lost reason updated',
                    'body'  => 'The lost reason has been updated successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Lost reason restored',
                    'body'  => 'The lost reason has been restored successfully.',
                ],
            ],

            'delete' => [
                'label' => 'Archive',

                'modal' => [
                    'heading'     => 'Archive lost reason',
                    'description' => 'The lost reason will move to the Archived tab. You can restore it at any time.',
                ],

                'notification' => [
                    'title' => 'Lost reason archived',
                    'body'  => 'The lost reason has been archived successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Lost reason deleted',
                    'body'  => 'The lost reason has been permanently deleted.',
                ],
            ],
        ],
    ],
];
