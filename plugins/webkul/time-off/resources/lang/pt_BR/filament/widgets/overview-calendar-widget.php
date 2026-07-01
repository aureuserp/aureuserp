<?php

return [
    'heading' => [
        'title' => 'Visão geral de ausências',
    ],

    'modal-actions' => [
        'edit' => [
            'title' => 'Editar',
            'notification' => [
                'title' => 'Ausência atualizada',
                'body' => 'A solicitação de ausência foi atualizada com sucesso.',
            ],
        ],

        'delete' => [
            'title' => 'Excluir',
        ],
    ],

    'view-action' => [
        'title' => 'Visualizar',
        'description' => 'Visualizar solicitação de ausência',
    ],

    'header-actions' => [
        'create' => [
            'title' => 'Nova ausência',
            'description' => 'Criar solicitação de ausência',

            'notification' => [
                'title' => 'Ausência criada',
                'body' => 'A solicitação de ausência foi criada com sucesso.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Colaborador não encontrado',
                    'body' => 'Adicione um colaborador ao seu perfil antes de criar uma solicitação de ausência.',
                ],
            ],
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type' => 'Tipo de ausência',
            'request-date-from' => 'Data inicial da solicitação',
            'request-date-to' => 'Data final da solicitação',
            'period' => 'Período',
            'half-day' => 'Meio dia',
            'requested-days' => 'Solicitado (dias/horas)',
            'description' => 'Descrição',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type' => 'Tipo de ausência',
            'request-date-from' => 'Data inicial da solicitação',
            'request-date-to' => 'Data final da solicitação',
            'description' => 'Descrição',
            'description-placeholder' => 'Nenhuma descrição fornecida',
            'duration' => 'Duração',
            'status' => 'Status',
        ],
    ],
];
