<?php

return [
    'navigation' => [
        'title' => 'Projects',
        'group' => 'Project',
    ],

    'global-search' => [
        'project-manager' => 'Project Manager',
        'customer'        => 'Customer',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'fields' => [
                    'name'             => 'Name',
                    'name-placeholder' => 'Project Name...',
                    'description'      => 'Description',
                ],
            ],

            'additional' => [
                'title' => 'Additional Information',

                'fields' => [
                    'project-manager'             => 'Project Manager',
                    'customer'                    => 'Customer',
                    'start-date'                  => 'Start Date',
                    'end-date'                    => 'End Date',
                    'allocated-hours'             => 'Allocated Hours',
                    'allocated-hours-helper-text' => 'In hours (Eg. 1.5 hours means 1 hour 30 minutes)',
                    'tags'                        => 'Tags',
                    'company'                     => 'Company',
                ],
            ],

            'settings' => [
                'title' => 'Settings',

                'fields' => [
                    'visibility'                   => 'Visibility',
                    'visibility-hint-tooltip'      => 'Allow employees to access your project or tasks by adding them as followers. They will automatically gain access to any tasks assigned to them..',
                    'private-description'          => 'Invited internal users only.',
                    'internal-description'         => 'All internal users can see.',
                    'public-description'           => 'Invited portal users and all internal users.',
                    'time-management'              => 'Time Management',
                    'allow-timesheets'             => 'Allow Timesheets',
                    'allow-timesheets-helper-text' => 'Log time on tasks and track progress',
                    'task-management'              => 'Task Management',
                    'allow-milestones'             => 'Allow Milestones',
                    'allow-milestones-helper-text' => 'Monitor key milestones that are essential for achieving success.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Name',
            'customer'        => 'Customer',
            'start-date'      => 'Start Date',
            'end-date'        => 'End Date',
            'planned-date'    => 'Planned Date',
            'remaining-hours' => 'Remaining Hours',
            'project-manager' => 'Project Manager',
        ],

        'groups' => [
            'stage'           => 'Stage',
            'project-manager' => 'Project Manager',
            'customer'        => 'Customer',
            'created-at'      => 'Created At',
        ],

        'filters' => [
            'name'             => 'Name',
            'visibility'       => 'Visibility',
            'start-date'       => 'Start Date',
            'end-date'         => 'End Date',
            'allow-timesheets' => 'Allow Timesheets',
            'allow-milestones' => 'Allow Milestones',
            'allocated-hours'  => 'Allocated Hours',
            'created-at'       => 'Created At',
            'updated-at'       => 'Updated At',
            'stage'            => 'Stage',
            'customer'         => 'Customer',
            'project-manager'  => 'Project Manager',
            'company'          => 'Company',
            'creator'          => 'Creator',
            'tags'             => 'Tags',
        ],

        'actions' => [
            'tasks'      => ':count Tasks',
            'milestones' => ':completed milestones completed out of :all',

            'restore' => [
                'notification' => [
                    'title' => 'Project restored',
                    'body'  => 'The project has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Project deleted',
                    'body'  => 'The project has been deleted successfully.',
                ],
            ],

            'force-delete' => [

                'notification' => [

                    'success' => [
                        'title' => 'Project permanently deleted',
                        'body'  => 'The project has been permanently deleted successfully.',
                    ],

                    'error' => [
                        'title' => 'Project cannot be permanently deleted',
                        'body'  => 'The project is associated with other records.',
                    ],

                ],
            ],

        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'General',

                'entries' => [
                    'name'             => 'Name',
                    'name-placeholder' => 'Project Name...',
                    'description'      => 'Description',
                ],
            ],

            'additional' => [
                'title' => 'Additional Information',

                'entries' => [
                    'project-manager'        => 'Project Manager',
                    'customer'               => 'Customer',
                    'project-timeline'       => 'Project Timeline',
                    'allocated-hours'        => 'Allocated Hours',
                    'allocated-hours-suffix' => ' Hours',
                    'remaining-hours'        => 'Remaining Hours',
                    'remaining-hours-suffix' => ' Hours',
                    'current-stage'          => 'Current Stage',
                    'tags'                   => 'Tags',
                ],
            ],

            'statistics' => [
                'title' => 'Statistics',

                'entries' => [
                    'total-tasks'         => 'Total Tasks',
                    'milestones-progress' => 'Milestones Progress',
                ],
            ],

            'record-information' => [
                'title' => 'Record Information',

                'entries' => [
                    'created-at'   => 'Created At',
                    'created-by'   => 'Created By',
                    'last-updated' => 'Last Updated',
                ],
            ],

            'settings' => [
                'title' => 'Project Settings',

                'entries' => [
                    'visibility'         => 'Visibility',
                    'timesheets-enabled' => 'Timesheets Enabled',
                    'milestones-enabled' => 'Milestones Enabled',
                ],
            ],
        ],
    ],
];
