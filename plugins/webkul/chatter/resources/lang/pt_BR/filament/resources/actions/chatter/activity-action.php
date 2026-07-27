<?php

return [
    'setup' => [
        'title' => 'Agendar atividade',
        'submit-action-title' => 'Agendar',

        'form' => [
            'fields' => [
                'activity-plan' => 'Plano de atividade',
                'plan-date' => 'Data do plano',
                'plan-summary' => 'Resumo do plano',
                'activity-type' => 'Tipo de atividade',
                'due-date' => 'Data de vencimento',
                'summary' => 'Resumo',
                'assigned-to' => 'Atribuído a',
                'log-note' => 'Registrar nota',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Atividade criada',
                    'body' => 'A atividade foi criada.',
                ],

                'warning'  => [
                    'title' => 'Nenhum arquivo novo',
                    'body' => 'Todos os arquivos já foram enviados.',
                ],

                'error' => [
                    'title' => 'Falha na criação da atividade',
                    'body' => 'Falha ao criar atividade ',
                ],
            ],
        ],
    ],
];
