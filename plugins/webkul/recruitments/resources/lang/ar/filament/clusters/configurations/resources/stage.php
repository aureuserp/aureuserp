<?php

return [
    'title' => 'Stages',

    'navigation' => [
        'title' => 'Stages',
        'group' => 'Job Positions',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'General Information',

                'fields' => [
                    'stage-name'   => 'Stage Name',
                    'sort'         => 'Sequence Order',
                    'requirements' => 'Requirements',
                ],
            ],

            'tooltips' => [
                'title'       => 'Tooltips',
                'description' => 'Define the custom label for application status.',

                'fields' => [
                    'gray-label'          => 'Gray Label',
                    'gray-label-tooltip'  => 'The label for the gray status.',
                    'red-label'           => 'Red Label',
                    'red-label-tooltip'   => 'The label for the red status.',
                    'green-label'         => 'Green Label',
                    'green-label-tooltip' => 'The label for the green status.',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Information',

                'fields' => [
                    'job-positions' => 'Job Positions',
                    'folded'        => 'Folded',
                    'hired-stage'   => 'Hired Stage',
                    'default-stage' => 'Default Stage',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Stage Name',
            'hired-stage'        => 'Hired Stage',
            'default-stage'      => 'Default Stage',
            'folded'             => 'Folded',
            'job-positions'      => 'Job Positions',
            'created-by'         => 'Created By',
            'created-at'         => 'Created At',
            'updated-at'         => 'Updated At',
        ],

        'filters' => [
            'name'         => 'Stage Name',
            'job-position' => 'Job Position',
            'folded'       => 'Folded',
            'gray-label'   => 'Gray Label',
            'red-label'    => 'Red Label',
            'green-label'  => 'Green Label',
            'created-by'   => 'Created By',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'groups' => [
            'job-position' => 'Job Position',
            'stage-name'   => 'Stage Name',
            'folded'       => 'Folded',
            'gray-label'   => 'Gray Label',
            'red-label'    => 'Red Label',
            'green-label'  => 'Green Label',
            'created-by'   => 'Created By',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Stages deleted',
                        'body'  => 'The Stages has been deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Stages could not be deleted',
                        'body'  => 'The Stages cannot be deleted because it is currently in use.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Stages deleted',
                    'body'  => 'The Stages has been deleted successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'New Stage',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'General Information',

                'entries' => [
                    'stage-name'   => 'Stage Name',
                    'sort'         => 'Sequence Order',
                    'requirements' => 'Requirements',
                ],
            ],

            'tooltips' => [
                'title'       => 'Tooltips',
                'description' => 'Define the custom label for application status.',

                'entries' => [
                    'gray-label'          => 'Gray Label',
                    'gray-label-tooltip'  => 'The label for the gray status.',
                    'red-label'           => 'Red Label',
                    'red-label-tooltip'   => 'The label for the red status.',
                    'green-label'         => 'Green Label',
                    'green-label-tooltip' => 'The label for the green status.',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Information',

                'entries' => [
                    'job-positions'      => 'Job Position',
                    'folded'             => 'Folded',
                    'hired-stage'        => 'Hired Stage',
                    'default-stage'      => 'Default Stage',
                ],
            ],
        ],
    ],

];
