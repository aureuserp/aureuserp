<?php

return [
    'title' => 'Puestos de Trabajo',

    'navigation' => [
        'title' => 'Puestos de Trabajo',
        'group' => 'Reclutamiento',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Información de Empleo',

                'fields' => [
                    'job-position-title'         => 'Título del Puesto de Trabajo',
                    'job-position-title-tooltip' => 'Ingrese el título oficial del puesto de trabajo',
                    'department'                 => 'Departamento',
                    'department-modal-title'     => 'Crear Departamento',
                    'company-modal-title'        => 'Crear Empresa',
                    'job-location'               => 'Ubicación del Trabajo',
                    'industry'                   => 'Industria',
                    'company'                    => 'Empresa',
                    'employment-type'            => 'Tipo de Empleo',
                    'recruiter'                  => 'Reclutador',
                    'interviewer'                => 'Entrevistador',
                ],
            ],

            'job-description' => [
                'title' => 'Descripción del Puesto',

                'fields' => [
                    'job-description'  => 'Descripción del Puesto',
                    'job-requirements' => 'Requisitos del Puesto',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Planificación de la Fuerza Laboral',

                'fields' => [
                    'recruitment-target' => 'Objetivo de Contratación',
                    'date-from'          => 'Fecha Desde',
                    'date-to'            => 'Fecha Hasta',
                    'expected-skills'    => 'Habilidades Esperadas',
                    'employment-type'    => 'Tipo de Empleo',
                    'status'             => 'Estado',
                ],
            ],

            'position-status' => [
                'title' => 'Estado del Puesto',

                'fields' => [
                    'status' => 'Estado',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Puesto de Trabajo',
            'department'         => 'Departamento',
            'job-position'       => 'Puesto de Trabajo',
            'company'            => 'Empresa',
            'expected-employees' => 'Empleados Esperados',
            'current-employees'  => 'Empleados Actuales',
            'status'             => 'Estado',
            'created-by'         => 'Creado Por',
            'created-at'         => 'Creado El',
            'updated-at'         => 'Actualizado El',
        ],

        'filters' => [
            'department'      => 'Departamento',
            'employment-type' => 'Tipo de Empleo',
            'job-position'    => 'Puesto de Trabajo',
            'company'         => 'Empresa',
            'status'          => 'Estado',
            'created-by'      => 'Creado Por',
            'updated-at'      => 'Actualizado El',
            'created-at'      => 'Creado El',
        ],

        'groups' => [
            'job-position'    => 'Puesto de Trabajo',
            'company'         => 'Empresa',
            'department'      => 'Departamento',
            'employment-type' => 'Tipo de Empleo',
            'created-by'      => 'Creado Por',
            'created-at'      => 'Creado El',
            'updated-at'      => 'Actualizado El',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Puesto de Trabajo restaurado',
                    'body'  => 'El Puesto de Trabajo ha sido restaurado exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Puesto de Trabajo eliminado',
                    'body'  => 'El Puesto de Trabajo ha sido eliminado exitosamente.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Puestos de Trabajo restaurados',
                    'body'  => 'Los Puestos de Trabajo han sido restaurados exitosamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Puestos de Trabajo eliminados',
                    'body'  => 'Los Puestos de Trabajo han sido eliminados exitosamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Puestos de Trabajo eliminados permanentemente',
                    'body'  => 'Los Puestos de Trabajo han sido eliminados permanentemente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Puestos de Trabajo',
                    'body'  => 'Los Puestos de Trabajo han sido creados exitosamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Información de Empleo',

                'entries' => [
                    'job-position-title' => 'Título del Puesto de Trabajo',
                    'department'         => 'Departamento',
                    'company'            => 'Empresa',
                    'employment-type'    => 'Tipo de Empleo',
                    'job-location'       => 'Ubicación del Trabajo',
                    'industry'           => 'Industria',
                ],
            ],
            'job-description' => [
                'title' => 'Descripción del Puesto',

                'entries' => [
                    'job-description'  => 'Descripción del Puesto',
                    'job-requirements' => 'Requisitos del Puesto',
                ],
            ],
            'work-planning' => [
                'title' => 'Planificación de la Fuerza Laboral',

                'entries' => [
                    'expected-employees' => 'Empleados Esperados',
                    'current-employees'  => 'Empleados Actuales',
                    'date-from'          => 'Fecha Desde',
                    'date-to'            => 'Fecha Hasta',
                    'recruitment-target' => 'Objetivo de Contratación',
                ],
            ],
            'position-status' => [
                'title' => 'Estado del Puesto',

                'entries' => [
                    'status' => 'Estado',
                ],
            ],
        ],
    ],
];
