<?php

return [
    'navigation' => [
        'title' => 'Puestos de trabajo',
        'group' => 'Puestos de trabajo',
    ],

    'global-search' => [
        'name'            => 'Puesto de trabajo',
        'department'      => 'Departamento',
        'employment-type' => 'Tipo de empleo',
        'company'         => 'Empresa',
        'created-by'      => 'Creado por',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Información del empleo',

                'fields' => [
                    'job-position-title'         => 'Título del puesto de trabajo',
                    'job-position-title-tooltip' => 'Introducir el título oficial del puesto de trabajo',
                    'department'                 => 'Departamento',
                    'department-modal-title'     => 'Crear departamento',
                    'job-location'               => 'Ubicación del empleo',
                    'industry'                   => 'Sector',
                    'company'                    => 'Empresa',
                    'employment-type'            => 'Tipo de empleo',
                    'interviewers'               => 'Entrevistadores',
                    'recruiter'                  => 'Reclutador',
                    'manager'                    => 'Responsable',
                ],
            ],

            'job-description' => [
                'title' => 'Descripción del empleo',

                'fields' => [
                    'job-description'  => 'Descripción del empleo',
                    'job-requirements' => 'Requisitos del empleo',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Planificación de personal',

                'fields' => [
                    'recruitment-target' => 'Objetivo de reclutamiento',
                    'date-from'          => 'Fecha desde',
                    'date-to'            => 'Fecha hasta',
                    'expected-skills'    => 'Competencias esperadas',
                    'employment-type'    => 'Tipo de empleo',
                    'status'             => 'Estado',
                ],
            ],

            'position-status' => [
                'title' => 'Estado del puesto',

                'fields' => [
                    'status' => 'Estado',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Puesto de trabajo',
            'department'         => 'Departamento',
            'job-position'       => 'Puesto de trabajo',
            'company'            => 'Empresa',
            'expected-employees' => 'Empleados esperados',
            'current-employees'  => 'Empleados actuales',
            'status'             => 'Estado',
            'created-by'         => 'Creado por',
            'created-at'         => 'Creado el',
            'updated-at'         => 'Actualizado el',
        ],

        'filters' => [
            'department'      => 'Departamento',
            'employment-type' => 'Tipo de empleo',
            'job-position'    => 'Puesto de trabajo',
            'company'         => 'Empresa',
            'status'          => 'Estado',
            'created-by'      => 'Creado por',
            'updated-at'      => 'Actualizado el',
            'created-at'      => 'Creado el',
        ],

        'groups' => [
            'job-position'    => 'Puesto de trabajo',
            'company'         => 'Empresa',
            'department'      => 'Departamento',
            'employment-type' => 'Tipo de empleo',
            'created-by'      => 'Creado por',
            'created-at'      => 'Creado el',
            'updated-at'      => 'Actualizado el',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Puesto de trabajo restaurado',
                    'body'  => 'El puesto de trabajo se ha restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Puesto de trabajo eliminado',
                    'body'  => 'El puesto de trabajo se ha eliminado correctamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Puestos de trabajo restaurados',
                    'body'  => 'Los puestos de trabajo se han restaurado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Puestos de trabajo eliminados',
                    'body'  => 'Los puestos de trabajo se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Puestos de trabajo eliminados permanentemente',
                    'body'  => 'Los puestos de trabajo se han eliminado permanentemente correctamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Puestos de trabajo',
                    'body'  => 'Los puestos de trabajo se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Información del empleo',

                'entries' => [
                    'job-position-title' => 'Título del puesto de trabajo',
                    'department'         => 'Departamento',
                    'company'            => 'Empresa',
                    'employment-type'    => 'Tipo de empleo',
                    'job-location'       => 'Ubicación del empleo',
                    'industry'           => 'Sector',
                    'manager'            => 'Responsable',
                    'recruiter'          => 'Reclutador',
                    'interviewers'       => 'Entrevistadores',
                ],
            ],
            'job-description' => [
                'title' => 'Descripción del empleo',

                'entries' => [
                    'job-description'  => 'Descripción del empleo',
                    'job-requirements' => 'Requisitos del empleo',
                ],
            ],
            'work-planning' => [
                'title' => 'Planificación de personal',

                'entries' => [
                    'expected-employees' => 'Empleados esperados',
                    'current-employees'  => 'Empleados actuales',
                    'date-from'          => 'Fecha desde',
                    'date-to'            => 'Fecha hasta',
                    'recruitment-target' => 'Objetivo de reclutamiento',
                ],
            ],
            'position-status' => [
                'title' => 'Estado del puesto',

                'entries' => [
                    'status' => 'Estado',
                ],
            ],
        ],
    ],
];
