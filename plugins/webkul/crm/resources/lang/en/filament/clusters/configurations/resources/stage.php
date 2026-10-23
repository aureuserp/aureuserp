<?php

return [
    'model-label'        => 'Stage',
    'plural-model-label' => 'Stages',

    'navigation' => [
        'title' => 'Stages',
        'group' => 'Pipeline',
    ],

    'form' => [
        'fields' => [
            'teams'                    => 'Sales Teams',
            'teams-helper-text'        => 'Teams whose pipeline shows this stage. Leave empty to share it with all teams.',
            'all-teams'                => 'All teams',
            'name'                     => 'Stage Name',
            'is-won'                   => 'Is Won Stage?',
            'is-won-helper-text'       => 'Opportunities moved to this stage are marked as won (probability 100%).',
            'requirements'             => 'Requirements',
            'requirements-helper-text' => 'Internal guidance on what must be done before moving an opportunity to this stage.',
        ],
    ],

    'table' => [
        'columns' => [
            'teams'         => 'Sales Teams',
            'name'          => 'Stage Name',
            'is-won'        => 'Won Stage',
            'requirements'  => 'Requirements',
            'opportunities' => 'Opportunities',
            'created-at'    => 'Created At',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Stage updated',
                    'body'  => 'The stage has been updated successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Stage deleted',
                    'body'  => 'The stage has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Stages deleted',
                    'body'  => 'The stages have been deleted successfully.',
                ],
            ],
        ],
    ],
];
