<?php

return [
    'title' => 'Tasks',

    'navigation' => [
        'title' => 'Tasks',
        'group' => 'Project',
    ],

    'global-search' => [
        'project'   => 'Project',
        'customer'  => 'Customer',
        'milestone' => 'Milestone',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'title'             => 'Title',
                    'title-placeholder' => 'Task Title...',
                    'tags'              => 'Tags',
                    'name'              => 'Name',
                    'color'             => 'Color',
                    'description'       => 'Description',
                    'project'           => 'Project',
                    'status'            => 'Status',
                    'start_date'        => 'Start Date',
                    'end_date'          => 'End Date',
                ],
            ],

            'additional' => [
                'title' => 'Additional Information',
            ],

            'settings' => [
                'title' => 'Settings',

                'fields' => [
                    'project'                     => 'Project',
                    'milestone'                   => 'Milestone',
                    'milestone-hint-text'         => 'Automatically deliver your services upon reaching a milestone by linking it to a sales order item.',
                    'name'                        => 'Name',
                    'deadline'                    => 'Deadline',
                    'is-completed'                => 'Is Completed',
                    'customer'                    => 'Customer',
                    'assignees'                   => 'Assignees',
                    'allocated-hours'             => 'Allocated Hours',
                    'allocated-hours-helper-text' => 'In hours (Eg. 1.5 hours means 1 hour 30 minutes)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                  => 'ID',
            'priority'            => 'Priority',
            'state'               => 'State',
            'new-state'           => 'New State',
            'update-state'        => 'Update State',
            'title'               => 'Title',
            'project'             => 'Project',
            'project-placeholder' => 'Private Task',
            'milestone'           => 'Milestone',
            'customer'            => 'Customer',
            'assignees'           => 'Assignees',
            'allocated-time'      => 'Allocated Time',
            'time-spent'          => 'Time Spent',
            'time-remaining'      => 'Time Remaining',
            'progress'            => 'Progress',
            'deadline'            => 'Deadline',
            'tags'                => 'Tags',
            'stage'               => 'Stage',
        ],

        'groups' => [
            'state'      => 'State',
            'project'    => 'Project',
            'milestone'  => 'Milestone',
            'customer'   => 'Customer',
            'deadline'   => 'Deadline',
            'stage'      => 'Stage',
            'created-at' => 'Created At',
        ],

        'filters' => [
            'title'             => 'Title',
            'priority'          => 'Priority',
            'low'               => 'Low',
            'high'              => 'High',
            'state'             => 'State',
            'tags'              => 'Tags',
            'allocated-hours'   => 'Allocated Hours',
            'total-hours-spent' => 'Total Hours Spent',
            'remaining-hours'   => 'Remaining Hours',
            'overtime'          => 'Overtime',
            'progress'          => 'Progress',
            'deadline'          => 'Deadline',
            'created-at'        => 'Created At',
            'updated-at'        => 'Updated At',
            'assignees'         => 'Assignees',
            'customer'          => 'Customer',
            'project'           => 'Project',
            'stage'             => 'Stage',
            'milestone'         => 'Milestone',
            'company'           => 'Company',
            'creator'           => 'Creator',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Task restored',
                    'body'  => 'The task has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Task deleted',
                    'body'  => 'The task has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Task force deleted',
                    'body'  => 'The task has been force deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tasks restored',
                    'body'  => 'The tasks has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tasks deleted',
                    'body'  => 'The tasks has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tasks force deleted',
                    'body'  => 'The tasks has been force deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'title'       => 'Title',
                    'state'       => 'State',
                    'tags'        => 'Tags',
                    'priority'    => 'Priority',
                    'description' => 'Description',
                ],
            ],

            'project-information' => [
                'title' => 'Project Information',

                'entries' => [
                    'project'   => 'Project',
                    'milestone' => 'Milestone',
                    'customer'  => 'Customer',
                    'assignees' => 'Assignees',
                    'deadline'  => 'Deadline',
                    'stage'     => 'Stage',
                ],
            ],

            'time-tracking' => [
                'title' => 'Time Tracking',

                'entries' => [
                    'allocated-time'        => 'Allocated Time',
                    'time-spent'            => 'Time Spent',
                    'time-spent-suffix'     => ' Hours',
                    'time-remaining'        => 'Time Remaining',
                    'time-remaining-suffix' => ' Hours',
                    'progress'              => 'Progress',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Information',
            ],

            'record-information' => [
                'title' => 'Record Information',

                'entries' => [
                    'created-at'   => 'Created At',
                    'created-by'   => 'Created By',
                    'last-updated' => 'Last Updated',
                ],
            ],

            'statistics' => [
                'title' => 'Statistics',

                'entries' => [
                    'sub-tasks'         => 'Sub Tasks',
                    'timesheet-entries' => 'Timesheet Entries',
                ],
            ],
        ],
    ],
];
