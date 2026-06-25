<?php

return [
    'title' => 'Horários de trabalho',

    'navigation' => [
        'title' => 'Horários de trabalho',
        'group' => 'Colaborador',
    ],

    'groups' => [
        'status'     => 'Status',
        'created-by' => 'Criado por',
        'created-at' => 'Criado em',
        'updated-at' => 'Atualizado em',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Informações gerais',
                'fields' => [
                    'name'                  => 'Nome',
                    'schedule-name'         => 'Nome do horário',
                    'schedule-name-tooltip' => 'Escreva um nome descritivo para o horário de trabalho.',
                    'timezone'              => 'Fuso horário',
                    'timezone-tooltip'      => 'Selecione o fuso horário para o horário de trabalho.',
                    'company'               => 'Empresa',
                ],
            ],

            'configuration' => [
                'title'  => 'Configuração de horas de trabalho',
                'fields' => [
                    'hours-per-day'                   => 'Horas por dia',
                    'hours-per-day-suffix'            => 'Horas',
                    'full-time-required-hours'        => 'Horas necessárias para tempo integral',
                    'full-time-required-hours-suffix' => 'Horas por semana',
                ],
            ],

            'flexibility' => [
                'title'  => 'Flexibilidade',
                'fields' => [
                    'status'                     => 'Status',
                    'two-weeks-calendar'         => 'Calendário de duas semanas',
                    'two-weeks-calendar-tooltip' => 'Ativar horário de trabalho alternado a cada duas semanas.',
                    'flexible-hours'             => 'Horários flexíveis',
                    'flexible-hours-tooltip'     => 'Permitir que colaboradores tenham horários de trabalho flexíveis.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'             => 'ID',
            'name'           => 'Nome do horário',
            'timezone'       => 'Fuso horário',
            'company'        => 'Empresa',
            'flexible-hours' => 'Horários flexíveis',
            'status'         => 'Status',
            'daily-hours'    => 'Horas diárias',
            'created-by'     => 'Criado por',
            'created-at'     => 'Criado em',
            'updated-at'     => 'Atualizado em',
        ],

        'filters' => [
            'company'                  => 'Empresa',
            'is-active'                => 'Status',
            'two-week-calendar'        => 'Calendário de duas semanas',
            'flexible-hours'           => 'Horários flexíveis',
            'timezone'                 => 'Fuso horário',
            'name'                     => 'Nome do horário',
            'attendance'               => 'Presença',
            'created-by'               => 'Criado por',
            'daily-hours'              => 'Horas diárias',
            'full-time-required-hours' => 'Horas necessárias para tempo integral',
            'updated-at'               => 'Atualizado em',
            'created-at'               => 'Criado em',
        ],

        'groups' => [
            'name'           => 'Nome do horário',
            'status'         => 'Status',
            'timezone'       => 'Fuso horário',
            'flexible-hours' => 'Horários flexíveis',
            'daily-hours'    => 'Horas diárias',
            'created-by'     => 'Criado por',
            'created-at'     => 'Criado em',
            'updated-at'     => 'Atualizado em',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Plano de calendário restaurado',
                    'body'  => 'O plano de calendário foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Plano de calendário excluído',
                    'body'  => 'O plano de calendário foi excluído com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Plano de calendário excluído permanentemente',
                    'body'  => 'O plano de calendário foi excluído permanentemente com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Planos de calendário restaurados',
                    'body'  => 'Os planos de calendário foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Planos de calendário excluídos',
                    'body'  => 'Os planos de calendário foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Planos de calendário excluídos permanentemente',
                    'body'  => 'Os planos de calendário foram excluídos permanentemente com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informações gerais',
                'entries' => [
                    'name'                  => 'Nome',
                    'schedule-name'         => 'Nome do horário',
                    'schedule-name-tooltip' => 'Escreva um nome descritivo para o horário de trabalho.',
                    'timezone'              => 'Fuso horário',
                    'timezone-tooltip'      => 'Selecione o fuso horário para o horário de trabalho.',
                    'company'               => 'Empresa',
                ],
            ],

            'configuration' => [
                'title'   => 'Configuração de horas de trabalho',
                'entries' => [
                    'hours-per-day'                   => 'Horas por dia',
                    'hours-per-day-suffix'            => ' horas',
                    'full-time-required-hours'        => 'Horas necessárias para tempo integral',
                    'full-time-required-hours-suffix' => ' Horas por semana',
                ],
            ],

            'flexibility' => [
                'title'   => 'Flexibilidade',
                'entries' => [
                    'status'                     => 'Status',
                    'two-weeks-calendar'         => 'Calendário de duas semanas',
                    'two-weeks-calendar-tooltip' => 'Ativar horário de trabalho alternado a cada duas semanas.',
                    'flexible-hours'             => 'Horários flexíveis',
                    'flexible-hours-tooltip'     => 'Permitir que colaboradores tenham horários de trabalho flexíveis.',
                ],
            ],
        ],
    ],
];
