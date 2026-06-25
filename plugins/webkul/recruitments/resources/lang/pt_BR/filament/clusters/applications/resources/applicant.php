<?php

return [
    'title' => 'Candidato',

    'navigation' => [
        'title' => 'Candidatos',
    ],

    'global-search' => [
        'department' => 'Departamento',
        'work-email' => 'E-mail de trabalho',
        'work-phone' => 'Telefone de trabalho',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informações gerais',

                'fields' => [
                    'evaluation-good'           => 'Avaliação: Boa',
                    'evaluation-very-good'      => 'Avaliação: Muito boa',
                    'evaluation-very-excellent' => 'Avaliação: Excelente',
                    'hired'                     => 'Contratado',
                    'candidate-name'            => 'Nome do candidato',
                    'email'                     => 'E-mails',
                    'phone'                     => 'Telefone',
                    'linkedin-profile'          => 'Perfil do LinkedIn',
                    'recruiter'                 => 'Recrutador',
                    'interviewer'               => 'Entrevistador',
                    'tags'                      => 'Tags',
                    'notes'                     => 'Notas',
                    'hired-date'                => 'Data de contratação',
                    'job-position'              => 'Cargos',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Formação e disponibilidade',

                'fields' => [
                    'degree'            => 'Grau',
                    'availability-date' => 'Data de disponibilidade',
                ],
            ],

            'department' => [
                'title' => 'Departamento',
            ],

            'salary' => [
                'title' => 'Salário esperado e proposto',

                'fields' => [
                    'expected-salary'       => 'Salário esperado',
                    'salary-proposed-extra' => 'Outro benefício',
                    'proposed-salary'       => 'Salário proposto',
                    'salary-expected-extra' => 'Outro benefício',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Origem e meio',

                'fields' => [
                    'source' => 'Origem',
                    'medium' => 'Médio',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'Nome do parceiro',
            'applied-on'         => 'Candidatou-se em',
            'job-position'       => 'Cargo',
            'stage'              => 'Etapa',
            'candidate-name'     => 'Nome do candidato',
            'evaluation'         => 'Avaliação',
            'application-status' => 'Status da candidatura',
            'tags'               => 'Tags',
            'refuse-reason'      => 'Motivo da recusa',
            'email'              => 'E-mail',
            'recruiter'          => 'Recrutador',
            'interviewer'        => 'Entrevistador',
            'candidate-phone'    => 'Telefone',
            'medium'             => 'Médio',
            'source'             => 'Origem',
            'salary-expected'    => 'Salário esperado',
            'availability-date'  => 'Data de disponibilidade',
        ],

        'filters' => [
            'source'                  => 'Origem',
            'medium'                  => 'Médio',
            'candidate'               => 'Candidato',
            'priority'                => 'Prioridade',
            'salary-proposed-extra'   => 'Extra do salário proposto',
            'salary-expected-extra'   => 'Extra do salário esperado',
            'applicant-notes'         => 'Observações do candidato',
            'create-date'             => 'Candidatou-se em',
            'date-closed'             => 'Data de contratação',
            'date-last-stage-updated' => 'Última etapa atualizada',
            'stage'                   => 'Etapa',
            'job-position'            => 'Cargo',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Candidato excluído',
                    'body'  => 'O candidato foi excluído com sucesso.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'Etapa',
            'job-position'   => 'Cargo',
            'candidate-name' => 'Nome do candidato',
            'responsible'    => 'Responsável',
            'creation-date'  => 'Data de criação',
            'hired-date'     => 'Data de contratação',
            'last-stage'     => 'Última etapa',
            'refuse-reason'  => 'Motivo da recusa',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Colaboradores excluídos',
                    'body'  => 'Os colaboradores foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Colaboradores excluídos',
                    'body'  => 'Os colaboradores foram excluídos com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Colaboradores restaurados',
                    'body'  => 'Os colaboradores foram restaurados com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'evaluation-good'           => 'Avaliação: Boa',
                    'evaluation-very-good'      => 'Avaliação: Muito boa',
                    'evaluation-very-excellent' => 'Avaliação: Excelente',
                    'hired'                     => 'Contratado',
                    'candidate-name'            => 'Nome do candidato',
                    'email'                     => 'E-mails',
                    'phone'                     => 'Telefone',
                    'linkedin-profile'          => 'Perfil do LinkedIn',
                    'recruiter'                 => 'Recrutador',
                    'interviewer'               => 'Entrevistador',
                    'tags'                      => 'Tags',
                    'notes'                     => 'Notas',
                    'job-position'              => 'Cargos',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Formação e disponibilidade',

                'entries' => [
                    'degree'            => 'Grau',
                    'availability-date' => 'Data de disponibilidade',
                ],
            ],

            'department' => [
                'title' => 'Departamento',
            ],

            'salary' => [
                'title' => 'Salário esperado e proposto',

                'entries' => [
                    'expected-salary'       => 'Salário esperado',
                    'salary-proposed-extra' => 'Outro benefício',
                    'proposed-salary'       => 'Salário proposto',
                    'salary-expected-extra' => 'Outro benefício',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Origem e meio',

                'entries' => [
                    'source' => 'Origem',
                    'medium' => 'Médio',
                ],
            ],
        ],
    ],
];
