<?php

return [
    'model-label'        => 'Atividade',
    'plural-model-label' => 'Atividades',

    'navigation' => [
        'title' => 'Minhas atividades',
    ],

    'views' => [
        'list'   => 'Lista',
        'kanban' => 'Kanban',
        'graph'  => 'Gráfico',
    ],

    'table' => [
        'columns' => [
            'due-date'      => 'Data de vencimento',
            'opportunity'   => 'Lead / Oportunidade',
            'customer'      => 'Cliente',
            'activity-type' => 'Tipo de atividade',
            'summary'       => 'Resumo',
            'assigned-to'   => 'Atribuído a',
            'stage'         => 'Etapa',
            'created-at'    => 'Criado em',
        ],

        'groups' => [
            'activity-type' => 'Tipo de atividade',
            'assigned-to'   => 'Atribuído a',
            'due-date'      => 'Data de vencimento',
        ],

        'filters' => [
            'due-status'    => 'Vencimento',
            'activity-type' => 'Tipo de atividade',
            'assigned-to'   => 'Atribuído a',
        ],

        'empty-state' => [
            'heading'     => 'Nenhuma atividade',
            'description' => 'Use Agendar atividade, ou o ícone de relógio em uma oportunidade, para planejar seu próximo passo.',
        ],
    ],

    'actions' => [
        'schedule' => [
            'label' => 'Agendar atividade',

            'modal' => [
                'heading' => 'Agendar atividade',
                'submit'  => 'Agendar',
            ],

            'form' => [
                'opportunity'   => 'Oportunidade',
                'activity-type' => 'Tipo de atividade',
                'due-date'      => 'Data de vencimento',
                'summary'       => 'Resumo',
                'assigned-to'   => 'Atribuído a',
                'note'          => 'Nota',
            ],

            'notification' => [
                'success' => [
                    'title' => 'Atividade agendada',
                    'body'  => 'A atividade foi agendada em ":opportunity".',
                ],

                'forbidden' => [
                    'title' => 'Não permitido',
                    'body'  => 'Você não tem permissão para agendar atividades nesta oportunidade.',
                ],
            ],
        ],

        'open' => [
            'label' => 'Abrir registro',
        ],

        'done' => [
            'label' => 'Marcar como concluída',

            'modal' => [
                'heading' => 'Marcar atividade como concluída',
                'submit'  => 'Concluir',
            ],

            'form' => [
                'feedback' => 'Feedback',
            ],

            'notification' => [
                'title' => 'Atividade concluída',
                'body'  => 'A atividade foi marcada como concluída e registrada na oportunidade.',
            ],
        ],

        'reschedule' => [
            'label' => 'Reagendar',

            'modal' => [
                'heading' => 'Reagendar atividade',
            ],

            'form' => [
                'due-date'    => 'Data de vencimento',
                'summary'     => 'Resumo',
                'assigned-to' => 'Atribuído a',
            ],

            'notification' => [
                'title' => 'Atividade reagendada',
                'body'  => 'A atividade foi atualizada.',
            ],
        ],

        'cancel' => [
            'label'      => 'Cancelar atividade',
            'bulk-label' => 'Cancelar atividades',

            'modal' => [
                'heading' => 'Cancelar atividade',
            ],

            'notification' => [
                'title' => 'Atividade cancelada',
                'body'  => 'A atividade foi removida.',
            ],
        ],
    ],
];
