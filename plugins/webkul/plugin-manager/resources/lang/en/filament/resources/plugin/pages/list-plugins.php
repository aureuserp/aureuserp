<?php

return [
    'navigation' => [
        'title' => 'Plugins',
    ],

    'tabs' => [
        'apps'          => 'Apps',
        'extra'         => 'Extra',
        'installed'     => 'Installed',
        'not-installed' => 'Not Installed',
    ],

    'header-actions' => [
        'sync' => [
            'label'                     => 'Sync Available Plugins',
            'modal-heading'             => 'Sync Plugins',
            'modal-description'         => 'This will scan and register any new plugins found.',
            'modal-submit-action-label' => 'Sync Plugins',

            'notification' => [
                'success' => [
                    'title' => 'Plugins Synced Successfully',
                    'body'  => 'Found and synced :count new plugin(s).',
                ],

                'error' => [
                    'title' => 'Plugin Sync Failed',
                    'body'  => 'An error (:error) occurred while syncing plugins. Please try again.',
                ],
            ],
        ],
    ],
];
