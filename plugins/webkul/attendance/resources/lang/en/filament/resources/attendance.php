<?php

return [
    'title' => 'Attendance',

    'navigation' => [
        'title' => 'Attendance',
    ],

    'form' => [
        'employee'                   => 'Employee',
        'work-date'                  => 'Work Date',
        'duplicate-day'              => ':employee already has an attendance record on :date.',
        'check-in'                   => 'Check In',
        'check-in-not-in-work-date'  => 'Check-in must be within the work date.',
        'check-out'                  => 'Check Out',
        'check-out-before-check-in'  => 'Check-out must be after check-in.',
        'check-out-not-in-work-date' => 'Check-out must be within the work date.',
        'long-shift-warning'         => 'Heads up: this shift is :hours hours long.',
        'source'                     => 'Source',
        'source-manual'              => 'Manual',
    ],

    'table' => [
        'columns' => [
            'employee'   => 'Employee',
            'work-date'  => 'Work Date',
            'check-in'   => 'Check In',
            'check-out'  => 'Check Out',
            'worked'     => 'Worked',
            'source'     => 'Source',
            'created-at' => 'Created At',
        ],

        'filters' => [
            'employee'         => 'Employee',
            'source'           => 'Source',
            'work-date'        => 'Work Date',
            'preset'           => 'Quick Period',
            'preset-today'     => 'Today',
            'preset-this-week' => 'This Week',
            'preset-this-month'=> 'This Month',
            'date-from'        => 'From',
            'date-to'          => 'To',
        ],

        'toolbar-actions' => [
            'export' => 'Export',
        ],

        'record-actions' => [
            'add-checkout' => 'Add Check-Out',
        ],
    ],
];
