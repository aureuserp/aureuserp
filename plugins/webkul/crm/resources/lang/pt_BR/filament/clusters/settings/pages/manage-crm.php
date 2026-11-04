<?php

return [
    'title' => 'Gerenciar CRM',

    'actions' => [
        'assign-now' => [
            'label'        => 'Atribuir agora',
            'description'  => 'Atribuir agora os leads sem responsável a equipes de vendas e vendedores, conforme as regras de atribuição.',
            'notification' => ':teams lead(s) atribuído(s) a equipes de vendas e :salespeople a vendedores.',
        ],
    ],

    'form' => [
        'use-leads' => [
            'label'  => 'Qualificação de leads',
            'helper' => 'Adicionar uma etapa de qualificação antes da criação de uma oportunidade.',
        ],

        'recurring-revenues' => [
            'label'  => 'Planos de receita recorrente',
            'helper' => 'Definir planos e receitas recorrentes nas oportunidades.',
        ],

        'multi-teams' => [
            'label'  => 'Vendedores em várias equipes',
            'helper' => 'Atribuir vendedores a várias equipes de vendas.',
        ],

        'auto-assignment' => [
            'label'        => 'Atribuição automática de leads',
            'helper'       => 'Atribuir leads periodicamente com base em regras. Todas as equipes de vendas usam esta configuração por padrão, salvo indicação em contrário.',
            'running'      => 'Execução',
            'manual'       => 'Manualmente',
            'auto'         => 'Repetidamente',
            'repeat-every' => 'Repetir a cada',
            'unit'         => 'Unidade',
            'next-run'     => 'Próxima execução: :date',

            'units' => [
                'minutes' => 'Minutos',
                'hours'   => 'Horas',
                'days'    => 'Dias',
                'weeks'   => 'Semanas',
            ],
        ],
    ],
];
