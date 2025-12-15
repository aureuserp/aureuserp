<?php

return [
    'navigation' => [
        'title' => 'Job Positions',
        'group' => 'Job Positions',
    ],

    'global-search' => [
        'name'            => 'Job Position',
        'department'      => 'Department',
        'employment-type' => 'Employment Type',
        'company'         => 'Company',
        'created-by'      => 'Created By',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Employment Information',

                'fields' => [
                    'job-position-title'         => 'Job Position Title',
                    'job-position-title-tooltip' => 'Enter the official job position title',
                    'department'                 => 'Department',
                    'department-modal-title'     => 'Department Create',
                    'job-location'               => 'Job Location',
                    'industry'                   => 'Industry',
                    'company'                    => 'Company',
                    'employment-type'            => 'Employment Type',
                    'interviewers'               => 'Interviewers',
                    'recruiter'                  => 'Recruiter',
                    'manager'                    => 'Manager',
                ],
            ],

            'job-description' => [
                'title' => 'Job Description',

                'fields' => [
                    'job-description'  => 'Job Description',
                    'job-requirements' => 'Job Requirements',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Workforce Planning',

                'fields' => [
                    'recruitment-target' => 'Recruitment Target',
                    'date-from'          => 'Date From',
                    'date-to'            => 'Date To',
                    'expected-skills'    => 'Expected Skills',
                    'employment-type'    => 'Employment Type',
                    'status'             => 'Status',
                ],
            ],

            'position-status' => [
                'title' => 'Position Status',

                'fields' => [
                    'status' => 'Status',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Job Position',
            'department'         => 'Department',
            'job-position'       => 'Job Position',
            'company'            => 'Company',
            'expected-employees' => 'Expected Employees',
            'current-employees'  => 'Current Employees',
            'status'             => 'Status',
            'created-by'         => 'Created By',
            'created-at'         => 'Created At',
            'updated-at'         => 'Updated At',
        ],

        'filters' => [
            'department'      => 'Department',
            'employment-type' => 'Employment Type',
            'job-position'    => 'Job Position',
            'company'         => 'Company',
            'status'          => 'Status',
            'created-by'      => 'Created By',
            'updated-at'      => 'Updated At',
            'created-at'      => 'Created At',
        ],

        'groups' => [
            'job-position'    => 'Job Position',
            'company'         => 'Company',
            'department'      => 'Department',
            'employment-type' => 'Employment Type',
            'created-by'      => 'Created By',
            'created-at'      => 'Created At',
            'updated-at'      => 'Updated At',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Job Position restored',
                    'body'  => 'The Job Position has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Job Position deleted',
                    'body'  => 'The Job Position has been deleted successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Job Positions restored',
                    'body'  => 'The Job Positions has been restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Job Positions deleted',
                    'body'  => 'The Job Positions has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Job Positions force deleted',
                    'body'  => 'The Job Positions has been force deleted successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Job Positions',
                    'body'  => 'The Job Positions has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Employment Information',

                'entries' => [
                    'job-position-title' => 'Job Position Title',
                    'department'         => 'Department',
                    'company'            => 'Company',
                    'employment-type'    => 'Employment Type',
                    'job-location'       => 'Job Location',
                    'industry'           => 'Industry',
                    'manager'            => 'Manager',
                    'recruiter'          => 'Recruiter',
                    'interviewers'       => 'Interviewers',
                ],
            ],
            'job-description' => [
                'title' => 'Job Description',

                'entries' => [
                    'job-description'  => 'Job Description',
                    'job-requirements' => 'Job Requirements',
                ],
            ],
            'work-planning' => [
                'title' => 'Workforce Planning',

                'entries' => [
                    'expected-employees' => 'Expected Employees',
                    'current-employees'  => 'Current Employees',
                    'date-from'          => 'Date From',
                    'date-to'            => 'Date To',
                    'recruitment-target' => 'Recruitment Target',
                ],
            ],
            'position-status' => [
                'title' => 'Position Status',

                'entries' => [
                    'status' => 'Status',
                ],
            ],
        ],
    ],
];
