<?php

return [
    'title' => 'Poste',

    'navigation' => [
        'title' => 'Postes',
    ],

    'table' => [
        'columns' => [
            'name'             => 'Nom',
            'manager-name'     => 'Responsable',
            'department-name'  => 'Département',
            'company-name'     => 'Société',
            'total-applicants' => ':count Candidats',
        ],

        'actions' => [
            'applications' => [
                'new-applications' => ':count Nouvelles candidatures',
            ],

            'to-recruitment' => [
                'to-recruitment' => ':count À recruter',
            ],

            'total-application' => [
                'total-application' => ':count Candidatures',
            ],
        ],
    ],

];
