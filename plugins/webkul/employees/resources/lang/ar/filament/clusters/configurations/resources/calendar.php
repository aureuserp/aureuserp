<?php

return [
    'title' => 'Working Schedules',

    'navigation' => [
        'title' => 'Working Schedules',
        'group' => 'Employee',
    ],

    'groups' => [
        'status'     => 'Status',
        'created-by' => 'Created By',
        'created-at' => 'Created At',
        'updated-at' => 'Updated At',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General Information',
                'fields' => [
                    'name'                  => 'Name',
                    'schedule-name'         => 'Schedule Name',
                    'schedule-name-tooltip' => 'Please write descriptive working schedule name.',
                    'timezone'              => 'Timezone',
                    'timezone-tooltip'      => 'Please select the timezone for the working schedule.',
                    'company'               => 'Company',
                ],
            ],

            'configuration' => [
                'title'  => 'Work Hours Configuration',
                'fields' => [
                    'hours-per-day'                   => 'Hours Per Day',
                    'hours-per-day-suffix'            => 'Hours',
                    'full-time-required-hours'        => 'Full Time Required Hours',
                    'full-time-required-hours-suffix' => 'Hours Per Week',
                ],
            ],

            'flexibility' => [
                'title'  => 'Flexibility',
                'fields' => [
                    'status'                     => 'Status',
                    'two-weeks-calendar'         => 'Two Weeks Calendar',
                    'two-weeks-calendar-tooltip' => 'Enable alternating two-week work schedule.',
                    'flexible-hours'             => 'Flexible Hours',
                    'flexible-hours-tooltip'     => 'Allow employees to have flexible work hours.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'             => 'ID',
            'name'           => 'Schedule Name',
            'timezone'       => 'Timezone',
            'company'        => 'Company',
            'flexible-hours' => 'Flexible Hours',
            'status'         => 'Status',
            'daily-hours'    => 'Daily Hours',
            'created-by'     => 'Created By',
            'created-at'     => 'Created At',
            'updated-at'     => 'Updated At',
        ],

        'filters' => [
            'company'           => 'Company',
            'is-active'         => 'Status',
            'two-week-calendar' => 'Two Weeks Calendar',
            'flexible-hours'    => 'Flexible Hours',
            'timezone'          => 'Timezone',
            'name'              => 'Schedule Name',
            'attendance'        => 'Attendance',
            'created-by'        => 'Created By',
            'daily-hours'       => 'Daily Hours',
            'updated-at'        => 'Updated At',
            'created-at'        => 'Created At',
        ],

        'groups' => [
            'name'           => 'Schedule Name',
            'status'         => 'Status',
            'timezone'       => 'Timezone',
            'flexible-hours' => 'Flexible Hours',
            'daily-hours'    => 'Daily Hours',
            'created-by'     => 'Created By',
            'created-at'     => 'Created At',
            'updated-at'     => 'Updated At',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Calendar Plan restored',
                    'body'  => 'The calendar plan has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Calendar Plan deleted',
                    'body'  => 'The calendar plan has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Calendar Plan force deleted',
                    'body'  => 'The calendar plan has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Calendar Plans restored',
                    'body'  => 'The calendar plans has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Calendar Plans deleted',
                    'body'  => 'The calendar plans has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Calendar Plans force deleted',
                    'body'  => 'The calendar plans has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'General Information',
                'entries' => [
                    'name'                  => 'Name',
                    'schedule-name'         => 'Schedule Name',
                    'schedule-name-tooltip' => 'Please write descriptive working schedule name.',
                    'timezone'              => 'Timezone',
                    'timezone-tooltip'      => 'Please select the timezone for the working schedule.',
                    'company'               => 'Company',
                ],
            ],

            'configuration' => [
                'title'   => 'Work Hours Configuration',
                'entries' => [
                    'hours-per-day'                   => 'Hours Per Day',
                    'hours-per-day-suffix'            => 'Hours',
                    'full-time-required-hours'        => 'Full Time Required Hours',
                    'full-time-required-hours-suffix' => 'Hours Per Week',
                ],
            ],

            'flexibility' => [
                'title'   => 'Flexibility',
                'entries' => [
                    'status'                     => 'Status',
                    'two-weeks-calendar'         => 'Two Weeks Calendar',
                    'two-weeks-calendar-tooltip' => 'Enable alternating two-week work schedule.',
                    'flexible-hours'             => 'Flexible Hours',
                    'flexible-hours-tooltip'     => 'Allow employees to have flexible work hours.',
                ],
            ],
        ],
    ],
];
