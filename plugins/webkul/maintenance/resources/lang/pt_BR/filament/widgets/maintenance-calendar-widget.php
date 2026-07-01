<?php

return [
    'heading' => [
        'title' => 'Calendário de manutenção',
    ],

    'config' => [
        'button-text' => [
            'today' => 'Hoje',
            'year'  => 'Ano',
            'month' => 'Mês',
            'week'  => 'Semana',
            'list'  => 'Lista',
        ],
    ],

    'header-actions' => [
        'create' => [
            'label'         => 'Nova solicitação',
            'modal-heading' => 'Nova solicitação de manutenção',
            'notification'  => [
                'success' => [
                    'title' => 'Solicitação de manutenção criada',
                    'body'  => 'A solicitação de manutenção foi criada com sucesso.',
                ],
                'error' => [
                    'title' => 'Solicitação de manutenção não pôde ser criada',
                    'body'  => 'Crie primeiro um etapa e uma equipe de manutenção.',
                ],
            ],
        ],
    ],

    'view-action' => [
        'label' => 'Visualizar',
    ],

    'modal-actions' => [
        'edit' => [
            'label' => 'Editar',
        ],
    ],

    'form' => [
        'fields' => [
            'subject'      => 'Assunto',
            'scheduled-at' => 'Agendado em',
        ],
    ],

    'infolist' => [
        'title'   => 'Solicitação de manutenção',
        'entries' => [
            'subject'          => 'Assunto',
            'date'             => 'Data',
            'time'             => 'Hora',
            'technician'       => 'Técnico',
            'priority'         => 'Prioridade',
            'maintenance-type' => 'Tipo de manutenção',
            'stage'            => 'Etapa',
        ],
    ],
];
