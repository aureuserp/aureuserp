<?php

return [
    'navigation' => [
        'title' => 'Cargos',
        'group' => 'Cargos',
    ],

    'global-search' => [
        'name' => 'Cargo',
        'department' => 'Departamento',
        'employment-type' => 'Tipo de vínculo',
        'company' => 'Empresa',
        'created-by' => 'Criado por',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Informações de vínculo',

                'fields' => [
                    'job-position-title' => 'Título do cargo',
                    'job-position-title-tooltip' => 'Informe o título oficial do cargo',
                    'department' => 'Departamento',
                    'department-modal-title' => 'Criar departamento',
                    'job-location' => 'Local da vaga',
                    'industry' => 'Setor',
                    'company' => 'Empresa',
                    'employment-type' => 'Tipo de vínculo',
                    'interviewers' => 'Entrevistadores',
                    'recruiter' => 'Recrutador',
                    'manager' => 'Gerente',
                ],
            ],

            'job-description' => [
                'title' => 'Descrição da vaga',

                'fields' => [
                    'job-description' => 'Descrição da vaga',
                    'job-requirements' => 'Requisitos da vaga',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Planejamento de força de trabalho',

                'fields' => [
                    'recruitment-target' => 'Meta de recrutamento',
                    'date-from' => 'Data inicial',
                    'date-to' => 'Data até',
                    'expected-skills' => 'Habilidades esperadas',
                    'employment-type' => 'Tipo de vínculo',
                    'status' => 'Status',
                ],
            ],

            'position-status' => [
                'title' => 'Status do cargo',

                'fields' => [
                    'status' => 'Status',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Cargo',
            'department' => 'Departamento',
            'job-position' => 'Cargo',
            'company' => 'Empresa',
            'expected-employees' => 'Colaboradores esperados',
            'current-employees' => 'Colaboradores atuais',
            'status' => 'Status',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'department' => 'Departamento',
            'employment-type' => 'Tipo de vínculo',
            'job-position' => 'Cargo',
            'company' => 'Empresa',
            'status' => 'Status',
            'created-by' => 'Criado por',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'job-position' => 'Cargo',
            'company' => 'Empresa',
            'department' => 'Departamento',
            'employment-type' => 'Tipo de vínculo',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Cargo restaurado',
                    'body' => 'O cargo foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cargo excluído',
                    'body' => 'O cargo foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Cargos restaurados',
                    'body' => 'Os cargos foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cargos excluídos',
                    'body' => 'Os cargos foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Cargos excluídos permanentemente',
                    'body' => 'Os cargos foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Cargos',
                    'body' => 'Os cargos foram criados com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Informações de vínculo',

                'entries' => [
                    'job-position-title' => 'Título do cargo',
                    'department' => 'Departamento',
                    'company' => 'Empresa',
                    'employment-type' => 'Tipo de vínculo',
                    'job-location' => 'Local da vaga',
                    'industry' => 'Setor',
                    'manager' => 'Gerente',
                    'recruiter' => 'Recrutador',
                    'interviewers' => 'Entrevistadores',
                ],
            ],
            'job-description' => [
                'title' => 'Descrição da vaga',

                'entries' => [
                    'job-description' => 'Descrição da vaga',
                    'job-requirements' => 'Requisitos da vaga',
                ],
            ],
            'work-planning' => [
                'title' => 'Planejamento de força de trabalho',

                'entries' => [
                    'expected-employees' => 'Colaboradores esperados',
                    'current-employees' => 'Colaboradores atuais',
                    'date-from' => 'Data inicial',
                    'date-to' => 'Data até',
                    'recruitment-target' => 'Meta de recrutamento',
                ],
            ],
            'position-status' => [
                'title' => 'Status do cargo',

                'entries' => [
                    'status' => 'Status',
                ],
            ],
        ],
    ],
];
