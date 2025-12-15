<?php

return [
    'title' => 'Public Holidays',

    'model-label' => 'Public holiday',

    'navigation' => [
        'title' => 'Public Holidays',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Name',
            'name-placeholder' => 'Enter the name of the public holiday',
            'date-from'        => 'Start Date',
            'date-to'          => 'End Date',
            'color'            => 'Color',
            'calendar'         => 'Calendar',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'company-name' => 'Company Name',
            'calendar'     => 'Calendar',
            'created-by'   => 'Created By',
            'date-from'    => 'Start Date',
            'date-to'      => 'End Date',
        ],

        'filters' => [
            'name'         => 'Name',
            'company-name' => 'Company Name',
            'created-by'   => 'Created By',
            'date-from'    => 'Start Date',
            'date-to'      => 'End Date',
            'created-at'   => 'Created At',
            'updated-at'   => 'Updated At',
        ],

        'groups' => [
            'name'         => 'Name',
            'company-name' => 'Company Name',
            'created-by'   => 'Created By',
            'date-from'    => 'Start Date',
            'date-to'      => 'End Date',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Public holiday updated',
                    'body'  => 'The public holiday has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Public holiday deleted',
                    'body'  => 'The public holiday has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Public holidays deleted',
                    'body'  => 'The public holidays has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'      => 'Name',
            'date-from' => 'Start Date',
            'date-to'   => 'End Date',
            'color'     => 'Color',
        ],
    ],
];
