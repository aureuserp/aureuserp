<?php

return [
    'title' => 'Candidato',

    'navigation' => [
        'title' => 'Candidatos',
    ],

    'global-search' => [
        'department' => 'Departamento',
        'work-email' => 'Correo del trabajo',
        'work-phone' => 'Teléfono del trabajo',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Información general',

                'fields' => [
                    'evaluation-good'           => 'Evaluación: Buena',
                    'evaluation-very-good'      => 'Evaluación: Muy buena',
                    'evaluation-very-excellent' => 'Evaluación: Excelente',
                    'hired'                     => 'Contratado',
                    'candidate-name'            => 'Nombre del candidato',
                    'email'                     => 'Correos',
                    'phone'                     => 'Teléfono',
                    'linkedin-profile'          => 'Perfil de LinkedIn',
                    'recruiter'                 => 'Reclutador',
                    'interviewer'               => 'Entrevistador',
                    'tags'                      => 'Etiquetas',
                    'notes'                     => 'Notas',
                    'hired-date'                => 'Fecha de contratación',
                    'job-position'              => 'Puestos de trabajo',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Formación y disponibilidad',

                'fields' => [
                    'degree'            => 'Titulación',
                    'availability-date' => 'Fecha de disponibilidad',
                ],
            ],

            'department' => [
                'title' => 'Departamento',
            ],

            'salary' => [
                'title' => 'Salario esperado y propuesto',

                'fields' => [
                    'expected-salary'       => 'Salario esperado',
                    'salary-proposed-extra' => 'Otro beneficio',
                    'proposed-salary'       => 'Salario propuesto',
                    'salary-expected-extra' => 'Otro beneficio',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Fuente y medio',

                'fields' => [
                    'source' => 'Fuente',
                    'medium' => 'Medio',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'Nombre del contacto',
            'applied-on'         => 'Fecha de candidatura',
            'job-position'       => 'Puesto de trabajo',
            'stage'              => 'Etapa',
            'candidate-name'     => 'Nombre del candidato',
            'evaluation'         => 'Evaluación',
            'application-status' => 'Estado de la candidatura',
            'tags'               => 'Etiquetas',
            'refuse-reason'      => 'Motivo de rechazo',
            'email'              => 'Correo',
            'recruiter'          => 'Reclutador',
            'interviewer'        => 'Entrevistador',
            'candidate-phone'    => 'Teléfono',
            'medium'             => 'Medio',
            'source'             => 'Fuente',
            'salary-expected'    => 'Salario esperado',
            'availability-date'  => 'Fecha de disponibilidad',
        ],

        'filters' => [
            'source'                  => 'Fuente',
            'medium'                  => 'Medio',
            'candidate'               => 'Candidato',
            'priority'                => 'Prioridad',
            'salary-proposed-extra'   => 'Extra de salario propuesto',
            'salary-expected-extra'   => 'Extra de salario esperado',
            'applicant-notes'         => 'Notas del candidato',
            'create-date'             => 'Fecha de candidatura',
            'date-closed'             => 'Fecha de contratación',
            'date-last-stage-updated' => 'Última actualización de etapa',
            'stage'                   => 'Etapa',
            'job-position'            => 'Puesto de trabajo',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidato eliminado',
                    'body'  => 'El candidato se ha eliminado correctamente.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'Etapa',
            'job-position'   => 'Puesto de trabajo',
            'candidate-name' => 'Nombre del candidato',
            'responsible'    => 'Responsable',
            'creation-date'  => 'Fecha de creación',
            'hired-date'     => 'Fecha de contratación',
            'last-stage'     => 'Última etapa',
            'refuse-reason'  => 'Motivo de rechazo',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Empleados eliminados',
                    'body'  => 'Los empleados se han eliminado correctamente.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Empleados eliminados',
                    'body'  => 'Los empleados se han eliminado correctamente.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Empleados restaurados',
                    'body'  => 'Los empleados se han restaurado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Información general',

                'entries' => [
                    'evaluation-good'           => 'Evaluación: Buena',
                    'evaluation-very-good'      => 'Evaluación: Muy buena',
                    'evaluation-very-excellent' => 'Evaluación: Excelente',
                    'hired'                     => 'Contratado',
                    'candidate-name'            => 'Nombre del candidato',
                    'email'                     => 'Correos',
                    'phone'                     => 'Teléfono',
                    'linkedin-profile'          => 'Perfil de LinkedIn',
                    'recruiter'                 => 'Reclutador',
                    'interviewer'               => 'Entrevistador',
                    'tags'                      => 'Etiquetas',
                    'notes'                     => 'Notas',
                    'job-position'              => 'Puestos de trabajo',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Formación y disponibilidad',

                'entries' => [
                    'degree'            => 'Titulación',
                    'availability-date' => 'Fecha de disponibilidad',
                ],
            ],

            'department' => [
                'title' => 'Departamento',
            ],

            'salary' => [
                'title' => 'Salario esperado y propuesto',

                'entries' => [
                    'expected-salary'       => 'Salario esperado',
                    'salary-proposed-extra' => 'Otro beneficio',
                    'proposed-salary'       => 'Salario propuesto',
                    'salary-expected-extra' => 'Otro beneficio',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Fuente y medio',

                'entries' => [
                    'source' => 'Fuente',
                    'medium' => 'Medio',
                ],
            ],
        ],
    ],
];
