<?php

return [
    'title' => 'Job Position',

    'navigation' => [
        'title' => 'Job Positions',
    ],

    'table' => [
        'columns' => [
            'name'             => 'Name',
            'manager-name'     => 'Manager',
            'department-name'  => 'Department',
            'company-name'     => 'Company',
            'total-applicants' => ':count Applicants',
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
