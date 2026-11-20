<?php

return [
    'everyone' => [
        'name'        => 'Everyone',
        'description' => 'Any email address can create a lead.',
    ],

    'partners' => [
        'name'        => 'Authenticated Partners',
        'description' => 'Only email addresses of existing contacts or users.',
    ],

    'followers' => [
        'name'        => 'Followers only',
        'description' => 'Only the team leader and the team members.',
    ],

    'employees' => [
        'name'        => 'Authenticated Employees',
        'description' => 'Only email addresses of users or employees.',
    ],
];
