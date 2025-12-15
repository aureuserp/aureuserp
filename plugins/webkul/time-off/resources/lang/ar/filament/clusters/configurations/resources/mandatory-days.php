<?php

return [
    'title' => 'Mandatory Days',

    'model-label' => 'Mandatory Day',

    'navigation' => [
        'title' => 'Mandatory Holidays',
    ],

    'form' => [
        'fields' => [
            'name'       => 'Name',
            'start-date' => 'Start Date',
            'end-date'   => 'End Date',
            'color'      => 'Color',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'company-name' => 'Company Name',
            'created-by'   => 'Created By',
            'start-date'   => 'Start Date',
            'end-date'     => 'End Date',
        ],

        'filters' => [
            'name'         => 'Name',
            'company-name' => 'Company Name',
            'created-by'   => 'Created By',
            'start-date'   => 'Start Date',
            'end-date'     => 'End Date',
        ],

        'groups' => [
            'name'         => 'Name',
            'company-name' => 'Company Name',
            'created-by'   => 'Created By',
            'start-date'   => 'Start Date',
            'end-date'     => 'End Date',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mandatory day updated',
                    'body'  => 'The mandatory day has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mandatory day deleted',
                    'body'  => 'The mandatory day has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mandatory days deleted',
                    'body'  => 'The mandatory days has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'       => 'Name',
            'start-date' => 'Start Date',
            'end-date'   => 'End Date',
            'color'      => 'Color',
        ],
    ],
];
