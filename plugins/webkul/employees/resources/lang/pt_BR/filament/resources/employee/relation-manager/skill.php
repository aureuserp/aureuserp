<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type' => 'Tipo de habilidade',
                'skill' => 'Habilidade',
                'skill-level' => 'Nível de habilidade',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type' => 'Tipo de habilidade',
            'skill' => 'Habilidade',
            'skill-level' => 'Nível de habilidade',
            'level-percent' => 'Percentual do nível',
            'created-by' => 'Criado por',
            'user' => 'Usuário',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'skill-type' => 'Tipo de habilidade',
        ],

        'header-actions' => [
            'add-skill' => 'Adicionar habilidade',
        ],

        'filters' => [
            'activity-type' => 'Tipo de atividade',
            'activity-status' => 'Status da atividade',
            'has-delay' => 'Tem atraso',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Habilidade atualizada',
                    'body' => 'A habilidade foi atualizada com sucesso.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Habilidade criada',
                    'body' => 'A habilidade foi criada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Habilidade excluída',
                    'body' => 'A habilidade foi excluída com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Habilidades excluídas',
                    'body' => 'As habilidades foram excluídas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type' => 'Tipo de habilidade',
            'skill' => 'Habilidade',
            'skill-level' => 'Nível de habilidade',
            'level-percent' => 'Percentual do nível',
        ],
    ],
];
