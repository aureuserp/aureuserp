<?php

return [
    'heading' => 'Conversas',

    'placeholders' => [
        'no-record-found' => 'Nenhum registro encontrado.',
        'loading'         => 'Carregando conversas...',
    ],

    'activity-infolist' => [
        'title' => 'Atividades',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'Cancelar atividade',
    ],

    'delete-message-action' => [
        'title' => 'Excluir mensagem',
    ],

    'edit-activity' => [
        'title' => 'Editar atividade',

        'form' => [
            'fields' => [
                'activity-plan' => 'Plano de atividade',
                'plan-date'     => 'Data do plano',
                'plan-summary'  => 'Resumo do plano',
                'activity-type' => 'Tipo de atividade',
                'due-date'      => 'Data de vencimento',
                'summary'       => 'Resumo',
                'assigned-to'   => 'Atribuído a',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'Atividade atualizada',
                    'body'  => 'A atividade foi atualizada com sucesso.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">Nota original</span>: :body</div>',
        'original-note' => '<br><div><span class="font-bold">Nota original</span>: :body</div>',
        'feedback'      => '<div><span class="font-bold">Feedback</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title'   => 'Marcar como concluída',
        'actions' => [
            'done' => [
                'label' => 'Concluído',
            ],
        ],
        'form'  => [
            'fields' => [
                'feedback' => 'Feedback',
            ],
        ],

        'footer-actions' => [
            'label' => 'Concluir e agendar próxima',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'Atividade marcada como concluída',
                        'body'  => 'A atividade foi marcada como concluída com sucesso.',
                    ],
                ],
            ],
        ],
    ],
];
