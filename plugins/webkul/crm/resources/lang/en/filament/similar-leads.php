<?php

return [
    'navigation' => [
        'label' => 'Similar Leads',
    ],

    'page' => [
        'title' => 'Similar leads: :name',

        'empty' => [
            'heading'     => 'No similar records',
            'description' => 'No other lead or opportunity has the same email, phone or customer.',
        ],
    ],

    'badges' => [
        'lost' => 'Lost',
    ],

    'merge' => [
        'label'        => 'Merge',
        'heading'      => 'Merge similar leads and opportunities',
        'submit'       => 'Merge',
        'description'  => 'Empty fields of the record you keep are filled from the others, and their notes, tags, messages, activities and attachments move to it. The other records are then deleted. The result is an opportunity if any of them is one, and it is active even if some were lost.',
        'chatter-note' => 'Merged into this record: :records',

        'fields' => [
            'records'         => 'Records to merge',
            'head'            => 'Record to keep',
            'head-helper'     => 'Its details are kept; empty fields are filled from the other records.',
            'salesperson'     => 'Salesperson',
            'team'            => 'Sales Team',
            'conflict-helper' => 'The selected records have different values; choose the one the merged record keeps.',
        ],

        'notifications' => [
            'invalid'     => 'Choose at least two records and the one to keep.',
            'forbidden'   => 'You are not allowed to merge these records (you need to edit the kept record and permanently delete the others).',
            'companies'   => 'Only records of the same company can be merged.',
            'choose-user' => 'Choose the salesperson of the merged record.',
            'choose-team' => 'Choose the sales team of the merged record.',

            'merged' => [
                'title' => 'Records merged',
                'body'  => ':count record(s) were merged into ":name".',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Lead',
            'type'        => 'Type',
            'contact'     => 'Contact Name',
            'email'       => 'Email',
            'phone'       => 'Phone',
            'stage'       => 'Stage',
            'salesperson' => 'Salesperson',
            'team'        => 'Sales Team',
            'created-at'  => 'Created On',
        ],
    ],

    'graph' => [
        'heading' => 'Records by stage',
        'dataset' => 'Records',
    ],
];
