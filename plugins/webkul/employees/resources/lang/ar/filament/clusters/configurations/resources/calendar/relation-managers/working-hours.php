<?php

return [
    'modal' => [
        'title' => 'Working Hours',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General Information',
                'fields' => [
                    'attendance-name' => 'Attendance Name',
                    'attendance-name' => 'Attendance Name',
                    'day-of-week'     => 'Day of Week',
                ],
            ],

            'timing-information' => [
                'title' => 'Timing Information',

                'fields' => [
                    'day-period' => 'Day Periods',
                    'week-type'  => 'Week Type',
                    'work-from'  => 'Work From',
                    'work-to'    => 'Work To',
                ],
            ],

            'date-information' => [
                'title' => 'Date Information',

                'fields' => [
                    'starting-date' => 'Starting Date',
                    'ending-date'   => 'Ending Date',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Information',

                'fields' => [
                    'durations-days' => 'Duration (Days)',
                    'display-type'   => 'Display Type',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Attendance Name',
            'day-of-week'   => 'Day of Week',
            'day-period'    => 'Day Periods',
            'work-from'     => 'Work From',
            'work-to'       => 'Work To',
            'starting-date' => 'Starting Date',
            'ending-date'   => 'Ending Date',
            'display-type'  => 'Display Type',
            'created-by'    => 'Created By',
            'created-at'    => 'Created At',
            'updated-at'    => 'Updated At',
        ],

        'groups' => [
            'activity-type' => 'Activity Type',
            'assignment'    => 'Assignment',
            'assigned-to'   => 'Assigned To',
            'interval'      => 'Interval',
            'delay-unit'    => 'Delay Unit',
            'delay-from'    => 'Delay From',
            'created-by'    => 'Created By',
            'created-at'    => 'Created At',
            'updated-at'    => 'Updated At',
        ],

        'filters' => [
            'display-type' => 'Display Type',
            'day-of-week'  => 'Day of Week',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Working Hours updated',
                    'body'  => 'The working hours has been updated successfully.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Working Hours created',
                    'body'  => 'The working hours has been created successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Working Hours deleted',
                    'body'  => 'The working hours has been deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Working Hours restored',
                    'body'  => 'The working hours has been restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Working Hours deleted',
                    'body'  => 'The working hours has been deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Working Hours deleted',
                    'body'  => 'The working hours has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Working Hours deleted',
                    'body'  => 'The working hours has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General Information',

                'entries' => [
                    'name'        => 'Attendance Name',
                    'day-of-week' => 'Day of Week',
                ],
            ],

            'timing-information' => [
                'title' => 'Timing Information',

                'entries' => [
                    'day-period' => 'Day Periods',
                    'week-type'  => 'Week Type',
                    'work-from'  => 'Work From',
                    'work-to'    => 'Work To',
                ],
            ],

            'date-information' => [
                'title' => 'Date Information',

                'entries' => [
                    'starting-date' => 'Starting Date',
                    'ending-date'   => 'Ending Date',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Information',

                'entries' => [
                    'durations-days' => 'Duration (Days)',
                    'display-type'   => 'Display Type',
                ],
            ],
        ],

        'note' => 'Note',
    ],
];
