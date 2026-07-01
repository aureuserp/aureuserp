<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Detalhes da atividade',

                'fields' => [
                    'activity-type' => 'Tipo de atividade',
                    'summary' => 'Resumo',
                    'note' => 'Nota',
                ],
            ],

            'assignment' => [
                'title' => 'Atribuição',

                'fields' => [
                    'assignment' => 'Atribuição',
                    'assignee' => 'Responsável',
                ],
            ],

            'delay-information' => [
                'title' => 'Informações de atraso',

                'fields' => [
                    'delay-count' => 'Contagem de atraso',
                    'delay-unit' => 'Unidade de atraso',
                    'delay-from' => 'Atraso a partir de',
                    'delay-from-helper-text' => 'Origem do cálculo do atraso',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Tipo de atividade',
            'summary' => 'Resumo',
            'assignment' => 'Atribuição',
            'assigned-to' => 'Atribuído a',
            'interval' => 'Intervalo',
            'delay-unit' => 'Unidade de atraso',
            'delay-from' => 'Atraso a partir de',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'activity-type' => 'Tipo de atividade',
            'assignment' => 'Atribuição',
            'assigned-to' => 'Atribuído a',
            'interval' => 'Intervalo',
            'delay-unit' => 'Unidade de atraso',
            'delay-from' => 'Atraso a partir de',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'activity-type' => 'Tipo de atividade',
            'activity-status' => 'Status da atividade',
            'has-delay' => 'Tem atraso',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Modelo de atividade atualizado',
                    'body' => 'O modelo de atividade foi atualizado com sucesso.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Modelo de atividade criado',
                    'body' => 'O modelo de atividade foi criado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Modelo de atividade excluído',
                    'body' => 'O modelo de atividade foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Modelos de atividade excluídos',
                    'body' => 'Os modelos de atividade foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Detalhes da atividade',

                'entries' => [
                    'activity-type' => 'Tipo de atividade',
                    'summary' => 'Resumo',
                ],
            ],

            'assignment' => [
                'title' => 'Atribuição',

                'entries' => [
                    'assignment' => 'Atribuição',
                    'assignee' => 'Responsável',
                ],
            ],

            'delay-information' => [
                'title' => 'Informações de atraso',

                'entries' => [
                    'delay-count' => 'Contagem de atraso',
                    'delay-unit' => 'Unidade de atraso',
                    'delay-from' => 'Atraso a partir de',
                    'delay-from-helper-text' => 'Origem do cálculo do atraso',
                ],
            ],
        ],

        'note' => 'Nota',
    ],
];
