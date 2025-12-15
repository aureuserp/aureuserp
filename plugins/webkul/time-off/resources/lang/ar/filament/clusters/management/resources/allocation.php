<?php

return [
    'title' => 'Allocation',

    'model-label' => 'Allocation',

    'navigation' => [
        'title' => 'Allocation',
    ],

    'form' => [
        'fields' => [
            'name'                => 'Name',
            'name-placeholder'    => 'Time Off Type (From validity start to validity end/no limit)',
            'time-off-type'       => 'Time Off Type',
            'employee-name'       => 'Employee Name',
            'allocation-type'     => 'Allocation Type',
            'validity-period'     => 'Validity Period',
            'date-from'           => 'Date From',
            'date-to'             => 'Date To',
            'date-to-placeholder' => 'No Limit',
            'allocation'          => 'Allocation',
            'allocation-suffix'   => 'Number of Days',
            'reason'              => 'Reason',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'   => 'Employee',
            'time-off-type'   => 'Time Off Type',
            'amount'          => 'Amount',
            'allocation-type' => 'Allocation Type',
            'status'          => 'Status',
        ],

        'groups' => [
            'time-off-type'   => 'Time Off Type',
            'employee-name'   => 'Employee Name',
            'allocation-type' => 'Allocation Type',
            'status'          => 'Status',
            'start-date'      => 'Start Date',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validate',
                    'approve'  => 'Approve',
                ],
                'notification' => [
                    'title' => 'Allocation approved approved',
                    'body'  => 'The allocation approved has been approved successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Allocation deleted',
                    'body'  => 'The allocation has been deleted successfully.',
                ],
            ],

            'refused' => [
                'title'        => 'Refuse',
                'notification' => [
                    'title' => 'Allocation refused',
                    'body'  => 'The allocation has been refused successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Allocations deleted',
                    'body'  => 'The allocations has been deleted successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title'   => 'Allocation Details',
                'entries' => [
                    'name'                => 'Name',
                    'time-off-type'       => 'Time Off Type',
                    'allocation-type'     => 'Allocation Type',
                ],
            ],

            'validity-period' => [
                'title'   => 'Validity Period',
                'entries' => [
                    'date-from' => 'Date From',
                    'date-to'   => 'Date To',
                    'reason'    => 'Reason',
                ],
            ],
            'allocation-status' => [
                'title'   => 'Allocation Status',
                'entries' => [
                    'date-to-placeholder' => 'No Limit',
                    'allocation'          => 'Number of Day(s)',
                    'allocation-value'    => ':days number of days',
                    'state'               => 'State',
                ],
            ],
        ],
    ],
];
