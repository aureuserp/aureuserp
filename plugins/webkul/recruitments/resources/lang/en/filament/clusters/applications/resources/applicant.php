<?php

return [
    'title' => 'Applicant',

    'navigation' => [
        'title' => 'Applicants',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'General Information',

                'fields' => [
                    'evaluation-good'           => 'Evaluation: Good',
                    'evaluation-very-good'      => 'Evaluation: Very Good',
                    'evaluation-very-excellent' => 'Evaluation: Very Excellent',
                    'hired'                     => 'Hired',
                    'candidate-name'            => 'Candidate name',
                    'email'                     => 'Emails',
                    'phone'                     => 'Phone',
                    'linkedin-profile'          => 'Linkedin Profile',
                    'recruiter'                 => 'Recruiter',
                    'interviewer'               => 'Interviewer',
                    'tags'                      => 'Tags',
                    'notes'                     => 'Notes',
                    'hired-date'                => 'Hired Date',
                    'job-position'              => 'Job Positions',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Education & Availability',

                'fields' => [
                    'degree'            => 'Degree',
                    'availability-date' => 'Availability Date',
                ],
            ],

            'department' => [
                'title' => 'Department',
            ],

            'salary' => [
                'title' => 'Expected & Proposed Salary',

                'fields' => [
                    'expected-salary'       => 'Expected Salary',
                    'salary-proposed-extra' => 'Other Benefit',
                    'proposed-salary'       => 'Proposed Salary',
                    'salary-expected-extra' => 'Other Benefit',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Source & Medium',

                'fields' => [
                    'source' => 'Source',
                    'medium' => 'Medium',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'Partner Name',
            'applied-on'         => 'Applied On',
            'job-position'       => 'Job Position',
            'stage'              => 'Stage',
            'candidate-name'     => 'Candidate Name',
            'evaluation'         => 'Evaluation',
            'application-status' => 'Application Status',
            'tags'               => 'Tags',
            'refuse-reason'      => 'Refuse Reason',
            'email'              => 'Email',
            'recruiter'          => 'Recruiter',
            'interviewer'        => 'Interviewer',
            'candidate-phone'    => 'Phone',
            'medium'             => 'Medium',
            'source'             => 'Source',
            'salary-expected'    => 'Expected Salary',
            'availability-date'  => 'Availability Date',
        ],

        'filters' => [
            'source'                  => 'Source',
            'medium'                  => 'Medium',
            'candidate'               => 'Candidate',
            'priority'                => 'Priority',
            'salary-proposed-extra'   => 'Salary Proposed Extra',
            'salary-expected-extra'   => 'Salary Expected Extra',
            'applicant-notes'         => 'Applicant Notes',
            'create-date'             => 'Applied On',
            'date-closed'             => 'Hired Date',
            'date-last-stage-updated' => 'Last Stage Updated',
            'stage'                   => 'Stage',
            'job-position'            => 'Job Position',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Applicant Deleted',
                    'body'  => 'The applicant was successfully deleted.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'Stage',
            'job-position'   => 'Job Position',
            'candidate-name' => 'Candidate Name',
            'responsible'    => 'Responsible',
            'creation-date'  => 'Creation Date',
            'hired-date'     => 'Hired Date',
            'last-stage'     => 'Last Stage',
            'refuse-reason'  => 'Refuse Reason',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Employees deleted',
                    'body'  => 'The employees has been deleted successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Employees deleted',
                    'body'  => 'The employees has been deleted successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Employees restored',
                    'body'  => 'The employees has been restored successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'General Information',

                'entries' => [
                    'evaluation-good'           => 'Evaluation: Good',
                    'evaluation-very-good'      => 'Evaluation: Very Good',
                    'evaluation-very-excellent' => 'Evaluation: Very Excellent',
                    'hired'                     => 'Hired',
                    'candidate-name'            => 'Candidate name',
                    'email'                     => 'Emails',
                    'phone'                     => 'Phone',
                    'linkedin-profile'          => 'Linkedin Profile',
                    'recruiter'                 => 'Recruiter',
                    'interviewer'               => 'Interviewer',
                    'tags'                      => 'Tags',
                    'notes'                     => 'Notes',
                    'job-position'              => 'Job Positions',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Education & Availability',

                'entries' => [
                    'degree'            => 'Degree',
                    'availability-date' => 'Availability Date',
                ],
            ],

            'department' => [
                'title' => 'Department',
            ],

            'salary' => [
                'title' => 'Expected & Proposed Salary',

                'entries' => [
                    'expected-salary'       => 'Expected Salary',
                    'salary-proposed-extra' => 'Other Benefit',
                    'proposed-salary'       => 'Proposed Salary',
                    'salary-expected-extra' => 'Other Benefit',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Source & Medium',

                'entries' => [
                    'source' => 'Source',
                    'medium' => 'Medium',
                ],
            ],
        ],
    ],
];
