<?php

return [
    'title' => 'Puesto de trabajo',

    'navigation' => [
        'title' => 'Puestos de trabajo',
    ],

    'table' => [
        'columns' => [
            'name'             => 'Nombre',
            'manager-name'     => 'Responsable',
            'department-name'  => 'Departamento',
            'company-name'     => 'Empresa',
            'total-applicants' => ':count candidatos',
        ],

        'actions' => [
            'applications' => [
                'new-applications' => ':count candidaturas nuevas',
            ],

            'to-recruitment' => [
                'to-recruitment' => ':count por reclutar',
            ],

            'total-application' => [
                'total-application' => ':count candidaturas',
            ],
        ],
    ],

];
