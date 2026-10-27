<?php

return [
    'model-label'        => 'Opportunity',
    'plural-model-label' => 'Opportunities',

    'views' => [
        'kanban' => 'Kanban',
        'list'   => 'List',
        'graph'  => 'Graph',
    ],

    'navigation' => [
        'title' => 'My Pipeline',
    ],

    'global-search' => [
        'customer' => 'Customer',
        'stage'    => 'Stage',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Opportunity',

                'fields' => [
                    'name'             => 'Opportunity',
                    'name-placeholder' => 'e.g. Product Pricing',
                    'expected-revenue' => 'Expected Revenue',
                    'probability'      => 'Probability',
                    'customer'         => 'Customer',
                    'email'            => 'Email',
                    'phone'            => 'Phone',
                ],
            ],

            'notes' => [
                'title' => 'Internal Notes',
            ],

            'sales' => [
                'title' => 'Sales',

                'fields' => [
                    'salesperson'      => 'Salesperson',
                    'team'             => 'Sales Team',
                    'company'          => 'Company',
                    'expected-closing' => 'Expected Closing',
                    'priority'         => 'Priority',
                    'tags'             => 'Tags',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'lost' => [
                'title' => 'Lost',

                'entries' => [
                    'lost-reason' => 'Lost Reason',
                    'feedback'    => 'Closing Note',
                ],
            ],

            'general' => [
                'title' => 'Opportunity',

                'entries' => [
                    'expected-revenue' => 'Expected Revenue',
                    'probability'      => 'Probability',
                    'customer'         => 'Customer',
                    'email'            => 'Email',
                    'phone'            => 'Phone',
                ],
            ],

            'notes' => [
                'title' => 'Internal Notes',
            ],

            'sales' => [
                'title' => 'Sales',

                'entries' => [
                    'salesperson'      => 'Salesperson',
                    'team'             => 'Sales Team',
                    'company'          => 'Company',
                    'expected-closing' => 'Expected Closing',
                    'priority'         => 'Priority',
                    'tags'             => 'Tags',
                    'closed-at'        => 'Closed At',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Opportunity',
            'customer'         => 'Customer',
            'stage'            => 'Stage',
            'expected-revenue' => 'Expected Revenue',
            'probability'      => 'Probability',
            'priority'         => 'Priority',
            'salesperson'      => 'Salesperson',
            'team'             => 'Sales Team',
            'expected-closing' => 'Expected Closing',
            'tags'             => 'Tags',
            'email'            => 'Email',
            'phone'            => 'Phone',
            'lost-reason'      => 'Lost Reason',
            'created-at'       => 'Created At',
        ],

        'summaries' => [
            'total' => 'Total',
        ],

        'groups' => [
            'stage'            => 'Stage',
            'team'             => 'Sales Team',
            'salesperson'      => 'Salesperson',
            'priority'         => 'Priority',
            'expected-closing' => 'Expected Closing',
        ],

        'actions' => [
            'schedule-activity' => [
                'label' => 'Schedule Activity',
            ],

            'restore' => [
                'label' => 'Restore',

                'notification' => [
                    'title' => 'Opportunity restored',
                    'body'  => 'The opportunity has been restored to the pipeline.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Opportunity deleted',
                    'body'  => 'The opportunity has been permanently deleted.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Opportunities restored',
                    'body'  => 'The opportunities have been restored to the pipeline.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Opportunities deleted',
                    'body'  => 'The opportunities have been permanently deleted.',
                ],
            ],
        ],
    ],

    'actions' => [
        'new-quotation' => [
            'label'             => 'New Quotation',
            'modal-heading'     => 'New quotation',
            'modal-description' => 'This opportunity has no customer yet. Choose the customer of the quotation.',
            'submit'            => 'Continue',
            'subheading'        => 'From opportunity: :name',
        ],

        'won' => [
            'label' => 'Won',

            'modal' => [
                'heading'     => 'Mark as won',
                'description' => 'The opportunity will move to the won stage with a probability of 100%.',
            ],

            'notification' => [
                'success' => [
                    'title' => 'Opportunity won',
                    'body'  => 'The opportunity has been marked as won.',
                ],

                'no-won-stage' => [
                    'title' => 'No won stage',
                    'body'  => 'Mark a stage as "Is Won Stage?" under CRM → Configuration → Stages first.',
                ],
            ],
        ],

        'lost' => [
            'label' => 'Lost',

            'modal' => [
                'heading' => 'Mark as lost',
                'submit'  => 'Mark as Lost',
            ],

            'form' => [
                'lost-reason' => 'Lost Reason',
                'feedback'    => 'Closing Note',
            ],

            'notification' => [
                'title' => 'Opportunity lost',
                'body'  => 'The opportunity has been marked as lost and moved to the Lost view.',
            ],
        ],
    ],

    'quotations' => [
        'title' => 'Quotations',

        'columns' => [
            'number'      => 'Number',
            'date'        => 'Date',
            'customer'    => 'Customer',
            'salesperson' => 'Salesperson',
            'total'       => 'Total',
            'status'      => 'Status',
        ],

        'empty' => [
            'heading'     => 'No quotations yet',
            'description' => 'Use "New Quotation" to create a quotation from this opportunity.',
        ],
    ],
];
