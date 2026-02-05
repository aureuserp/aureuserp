<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Time Off created',
            'body'  => 'The time off has been created successfully.',
        ],

        'overlap' => [
            'title' => 'Overlapping Leave Request',
            'body'  => 'The selected leave dates overlap with an existing request. Please choose different dates.',
        ],

        'warning' => [
            'title' => 'You don\'t have an employee account',
            'body'  => 'You don\'t have an employee account. Please contact your administrator.',
        ],

        'invalid_half_day_leave' => [
            'title' => 'Invalid Leave Request',
            'body'  => 'Half-day leave can only be applied for a single day.',
        ],

        'leave_request_denied_no_allocation' => [
            'title' => 'Leave Request Denied',
            'body'  => 'You do not have any allocated leave for :leaveType.',
        ],

        'leave_request_denied_insufficient_balance' => [
            'title' => 'Leave Request Denied',
            'body'  => 'Insufficient leave balance. You have :available_balance day(s) available. Requested: :requested_days day(s).',
        ],
    ],
];
