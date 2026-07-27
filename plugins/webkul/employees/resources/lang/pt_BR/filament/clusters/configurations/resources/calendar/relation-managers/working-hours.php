<?php

return [
    'modal' => [
        'title' => 'Horário de trabalho',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',
                'fields' => [
                    'attendance-name' => 'Nome da presença',
                    'attendance-name' => 'Nome da presença',
                    'day-of-week' => 'Dia da semana',
                ],
            ],

            'timing-information' => [
                'title' => 'Informações de horário',

                'fields' => [
                    'day-period' => 'Períodos do dia',
                    'week-type' => 'Tipo de semana',
                    'work-from' => 'Trabalhar de',
                    'work-to' => 'Trabalhar até',
                ],
            ],

            'date-information' => [
                'title' => 'Informações de data',

                'fields' => [
                    'starting-date' => 'Data inicial',
                    'ending-date' => 'Data final',
                ],
            ],

            'additional-information' => [
                'title' => 'Informações adicionais',

                'fields' => [
                    'durations-days' => 'Duração (dias)',
                    'display-type' => 'Tipo de exibição',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome da presença',
            'day-of-week' => 'Dia da semana',
            'day-period' => 'Períodos do dia',
            'work-from' => 'Trabalhar de',
            'work-to' => 'Trabalhar até',
            'starting-date' => 'Data inicial',
            'ending-date' => 'Data final',
            'display-type' => 'Tipo de exibição',
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
            'display-type' => 'Tipo de exibição',
            'day-of-week' => 'Dia da semana',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Horas de trabalho atualizadas',
                    'body' => 'As horas de trabalho foram atualizadas com sucesso.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Horas de trabalho criadas',
                    'body' => 'As horas de trabalho foram criadas com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Horas de trabalho excluídas',
                    'body' => 'As horas de trabalho foram excluídas com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Horas de trabalho restauradas',
                    'body' => 'As horas de trabalho foram restauradas com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Horas de trabalho excluídas',
                    'body' => 'As horas de trabalho foram excluídas com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Horas de trabalho excluídas',
                    'body' => 'As horas de trabalho foram excluídas com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Horas de trabalho excluídas',
                    'body' => 'As horas de trabalho foram excluídas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'name' => 'Nome da presença',
                    'day-of-week' => 'Dia da semana',
                ],
            ],

            'timing-information' => [
                'title' => 'Informações de horário',

                'entries' => [
                    'day-period' => 'Períodos do dia',
                    'week-type' => 'Tipo de semana',
                    'work-from' => 'Trabalhar de',
                    'work-to' => 'Trabalhar até',
                ],
            ],

            'date-information' => [
                'title' => 'Informações de data',

                'entries' => [
                    'starting-date' => 'Data inicial',
                    'ending-date' => 'Data final',
                ],
            ],

            'additional-information' => [
                'title' => 'Informações adicionais',

                'entries' => [
                    'durations-days' => 'Duração (dias)',
                    'display-type' => 'Tipo de exibição',
                ],
            ],
        ],

        'note' => 'Nota',
    ],
];
