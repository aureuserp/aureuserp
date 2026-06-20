<?php

return [
    'title' => 'Departamentos',

    'navigation' => [
        'title' => 'Departamentos',
        'group' => 'Colaboradores',
    ],

    'global-search' => [
        'department-manager' => 'Gerente',
        'company' => 'Empresa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'fields' => [
                    'name' => 'Nome',
                    'manager' => 'Gerente',
                    'parent-department' => 'Departamento superior',
                    'manager-placeholder' => 'Selecionar gerente',
                    'company' => 'Empresa',
                    'company-placeholder' => 'Selecionar empresa',
                    'color' => 'Cor',
                ],
            ],

            'additional' => [
                'title' => 'Informações adicionais',
                'description' => 'Informações adicionais sobre este departamento.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'manager-name' => 'Gerente',
            'company-name' => 'Empresa',
        ],

        'groups' => [
            'name' => 'Nome',
            'manager' => 'Gerente',
            'company' => 'Empresa',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'name' => 'Nome',
            'manager-name' => 'Gerente',
            'company-name' => 'Empresa',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Departamento restaurado',
                    'body' => 'O departamento foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departamento excluído',
                    'body' => 'O departamento foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Departamento excluído permanentemente',
                    'body' => 'O departamento foi excluído permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Departamentos restaurados',
                    'body' => 'Os departamentos foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departamentos excluídos',
                    'body' => 'Os departamentos foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Departamentos excluídos permanentemente',
                    'body' => 'Os departamentos foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Geral',

                'entries' => [
                    'name' => 'Nome',
                    'manager' => 'Gerente',
                    'company' => 'Empresa',
                    'color' => 'Cor',
                    'hierarchy-title' => 'Organização do departamento',
                ],
            ],
        ],
    ],
];
