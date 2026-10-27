<?php

return [
    'navigation' => [
        'title' => 'My Quotations',
    ],

    'pages' => [
        'list' => [
            'title' => 'My Quotations',
        ],
    ],

    'views' => [
        'list'   => 'List',
        'kanban' => 'Kanban',
        'graph'  => 'Graph',
    ],

    'tabs' => [
        'opportunity' => 'Opportunity',
    ],

    'board' => [
        'unauthorized' => 'You cannot move this quotation to that column.',

        'card' => [
            'open'        => 'Open :name',
            'customer'    => 'Customer',
            'total'       => 'Total',
            'date'        => 'Order Date',
            'salesperson' => 'Salesperson',
        ],
    ],

    'actions' => [
        'group' => [
            'label' => 'Actions',
        ],

        'duplicate' => [
            'label'        => 'Duplicate',
            'notification' => ':count quotation(s) duplicated.',
        ],

        'mark-as-sent' => [
            'label'        => 'Mark Quotation as Sent',
            'notification' => ':count quotation(s) marked as sent.',
        ],

        'send-email' => [
            'label'        => 'Send email',
            'description'  => 'Each selected quotation is emailed to its customer with the quotation PDF attached. Draft quotations are marked as sent.',
            'submit'       => 'Send',
            'subject'      => ':customer Quotation (Ref :name)',
            'body'         => 'Dear :customer,<br><br>Your quotation <strong>:name</strong> amounting to <strong>:amount</strong> is ready for review.<br><br>Should you have any questions, please feel free to reach out to us.',
            'notification' => ':sent quotation(s) sent, :skipped skipped (not a quotation, no customer email or no access).',
        ],

        'confirm' => [
            'label'        => 'Confirm',
            'description'  => 'The selected quotations become sales orders.',
            'notification' => ':confirmed quotation(s) confirmed, :skipped skipped.',
        ],
    ],

    'team-filter' => [
        'label' => 'Sales team: :team',
        'clear' => 'Show all',
    ],
];
