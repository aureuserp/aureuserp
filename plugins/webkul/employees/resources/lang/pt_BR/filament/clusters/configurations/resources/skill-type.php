<?php

return [
    'title' => 'Tipos de habilidade',

    'navigation' => [
        'title' => 'Tipos de habilidade',
        'group' => 'Funcionário',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name' => 'Nome',
                'name-placeholder' => 'Informe o nome do tipo de habilidade',
                'color' => 'Cor',
                'status' => 'Status',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Tipo de habilidade',
            'status' => 'Status',
            'color' => 'Cor',
            'skills' => 'Habilidades',
            'levels' => 'Níveis',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'skill-levels' => 'Níveis de habilidade',
            'skills' => 'Habilidades',
            'created-by' => 'Criado por',
            'status' => 'Status',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'name' => 'Tipo de habilidade',
            'color' => 'Cor',
            'status' => 'Status',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipo de habilidade restaurado',
                    'body' => 'O tipo de habilidade foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de habilidade excluído',
                    'body' => 'O tipo de habilidade foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tipos de habilidade restaurados',
                    'body' => 'Os tipos de habilidade foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipos de habilidade excluídos',
                    'body' => 'Os tipos de habilidade foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tipos de habilidade excluídos permanentemente',
                    'body' => 'Os tipos de habilidade foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tipos de habilidade',
                    'body' => 'Os tipos de habilidade foram criados com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name' => 'Tipo de habilidade',
                'color' => 'Cor',
                'status' => 'Status',
            ],
        ],
    ],
];
