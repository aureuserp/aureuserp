<?php

return [
    'title' => 'Candidate',

    'navigation' => [
        'title' => 'Candidates',
    ],

    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Basic Information',

                'fields' => [
                    'full-name' => 'Full Name',
                    'email'     => 'Email Address',
                    'phone'     => 'Phone Number',
                    'linkedin'  => 'LinkedIn Profile',
                    'contact'   => 'Contact',
                ],
            ],

            'additional-details' => [
                'title' => 'Additional Details',

                'fields' => [
                    'company'           => 'Company',
                    'degree'            => 'Degree',
                    'tags'              => 'Tags',
                    'manager'           => 'Manager',
                    'availability-date' => 'Availability Date',

                    'priority-options' => [
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Status',

                'fields' => [
                    'active'     => 'Active',
                    'evaluation' => 'Evaluation',
                ],
            ],

            'communication' => [
                'title' => 'Communication',

                'fields' => [
                    'cc-email'      => 'CC Email',
                    'email-bounced' => 'Email Bounced',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Full Name',
            'tags'       => 'Tags',
            'evaluation' => 'Evaluation',
        ],

        'filters' => [
            'company'      => 'Company',
            'partner-name' => 'Contact',
            'degree'       => 'Degree',
            'manager-name' => 'Manager',
        ],

        'groups' => [
            'manager-name' => 'Manager',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidate Deleted',
                    'body'  => 'The candidates has been deleted successfully.',
                ],
            ],

            'empty-state-actions' => [
                'create' => [
                    'notification' => [
                        'title' => 'Candidate Created',
                        'body'  => 'The candidates has been created successfully.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidates Deleted',
                    'body'  => 'The candidates has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Basic Information',

                'entries' => [
                    'full-name' => 'Full Name',
                    'email'     => 'Email Address',
                    'phone'     => 'Phone Number',
                    'linkedin'  => 'LinkedIn Profile',
                    'contact'   => 'Contact',
                ],
            ],

            'additional-details' => [
                'title' => 'Additional Details',

                'entries' => [
                    'company'           => 'Company',
                    'degree'            => 'Degree',
                    'tags'              => 'Tags',
                    'manager'           => 'Manager',
                    'availability-date' => 'Availability Date',

                    'priority-options' => [
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Status',

                'entries' => [
                    'active'     => 'Active',
                    'evaluation' => 'Evaluation',
                ],
            ],

            'communication' => [
                'title' => 'Communication',

                'entries' => [
                    'cc-email'      => 'CC Email',
                    'email-bounced' => 'Email Bounced',
                ],
            ],
        ],
    ],
];
