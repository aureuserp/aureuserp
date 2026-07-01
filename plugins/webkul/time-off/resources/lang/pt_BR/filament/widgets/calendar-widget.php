<?php

return [
    'heading' => [
        'title' => 'Solicitações de ausência',
    ],

    'modal-actions' => [
        'edit' => [
            'title' => 'Editar',
            'duration-display' => ':count dia útil|:count dias úteis',
            'duration-display-with-weekend' => ':count dia útil (+ :weekend dia de fim de semana)|:count dias úteis (+ :weekend dias de fim de semana)',

            'notification' => [
                'title' => 'Ausência atualizada',
                'body' => 'Sua solicitação de ausência foi atualizada com sucesso.',
            ],
        ],

        'delete' => [
            'title' => 'Excluir',
        ],
    ],

    'config' => [
        'button-text' => [
            'today' => 'Hoje',
            'month' => 'Mês',
            'week' => 'Semana',
            'list' => 'Lista',
        ],
    ],

    'view-action' => [
        'title' => 'Visualizar',
        'description' => 'Visualizar solicitação de ausência',
    ],

    'notifications' => [
        'employee-not-found' => [
            'title' => 'Colaborador não encontrado',
            'body' => 'Adicione um colaborador ao seu perfil antes de solicitar ausência.',
        ],

        'error' => [
            'title' => 'Algo deu errado',
            'body' => 'Não foi possível processar sua solicitação de ausência. Tente novamente.',
        ],
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

            'success' => [
                'notification' => [
                    'title' => 'Ausência criada',
                    'body' => 'Sua solicitação de ausência foi criada com sucesso.',
                ],
            ],
        ],
    ],

    'form' => [
        'title' => 'Solicitação de ausência',
        'description' => 'Crie ou edite sua solicitação de ausência com os seguintes detalhes:',

        'fields' => [
            'time-off-type' => 'Tipo de ausência',
            'time-off-type-placeholder' => 'Selecione um tipo de ausência',
            'time-off-type-helper' => 'Selecione o tipo de ausência que você está solicitando.',
            'request-date-from' => 'Data inicial da solicitação',
            'request-date-to' => 'Data final da solicitação',
            'period' => 'Período',
            'half-day' => 'Meio dia',
            'half-day-helper' => 'Alternar para licença de meio dia.',
            'requested-days' => 'Solicitado (dias/horas)',
            'description' => 'Descrição',
            'description-placeholder' => 'Nenhuma descrição fornecida',
            'description-helper' => 'Forneça uma breve descrição da sua solicitação de ausência.',
            'duration' => 'Duração',
            'please-select-dates' => 'Selecione a data inicial e final da solicitação.',
        ],
    ],

    'infolist' => [
        'title' => 'Detalhes da ausência',
        'description' => 'Aqui estão os detalhes da sua solicitação de ausência:',
        'entries'     => [
            'time-off-type' => 'Tipo de ausência',
            'request-date-from' => 'Data inicial da solicitação',
            'request-date-to' => 'Data final da solicitação',
            'description' => 'Descrição',
            'description-placeholder' => 'Nenhuma descrição fornecida',
            'duration' => 'Duração',
            'status' => 'Status',
        ],
    ],

    'events' => [
        'title' => ':name em :status: :days dia(s)',
    ],
];
