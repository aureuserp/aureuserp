<?php

return [
    'title' => 'Job Position',

    'navigation' => [
        'title' => 'Job Positions',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'manager-name' => 'Manager',
            'company-name' => 'Company',
        ],

        'actions' => [
            'applications' => [
                'new-applications' => ':count New Applications',
            ],

            'to-recruitment' => [
                'to-recruitment' => ':count To Recruitment',
            ],

            'total-application' => [
                'total-application' => ':count Applications',
            ],
        ],
    ],

];
