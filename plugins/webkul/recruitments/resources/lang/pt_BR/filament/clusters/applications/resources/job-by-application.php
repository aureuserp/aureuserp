<?php

return [
    'title' => 'Cargo',

    'navigation' => [
        'title' => 'Cargos',
    ],

    'table' => [
        'columns' => [
            'name'             => 'Nome',
            'manager-name'     => 'Gerente',
            'department-name'  => 'Departamento',
            'company-name'     => 'Empresa',
            'total-applicants' => ':count candidatos',
        ],

        'actions' => [
            'applications' => [
                'new-applications' => ':count novas candidaturas',
            ],

            'to-recruitment' => [
                'to-recruitment' => ':count para recrutamento',
            ],

            'total-application' => [
                'total-application' => ':count candidaturas',
            ],
        ],
    ],

];
