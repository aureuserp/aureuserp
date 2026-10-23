<?php

return [
    'navigation' => [
        'title' => 'Configurações',
    ],

    'title' => 'Configurações',

    'actions' => [
        'assign-now' => [
            'label'        => 'Atribuir agora',
            'description'  => 'Atribuir agora os leads sem responsável a equipes de vendas e vendedores, conforme as regras de atribuição.',
            'notification' => ':teams lead(s) atribuído(s) a equipes de vendas e :salespeople a vendedores.',
        ],
    ],

    'form' => [
        'scoring' => [
            'section'            => 'Pontuação preditiva de leads',
            'description'        => 'A probabilidade de ganhar cada lead e oportunidade aberta é calculada a partir do histórico de ganhos e perdas. A etapa e a equipe de vendas são sempre usadas.',
            'variables'          => 'Considerar também',
            'variables-helper'   => 'Quais dados de um registro a probabilidade considera.',
            'start-date'         => 'Considerar leads criados a partir de',
            'start-date-helper'  => 'Deixe vazio para usar todo o histórico.',
            'update'             => 'Atualizar probabilidades',
            'update-description' => 'Salva estas escolhas, reaprende com o histórico de ganhos e perdas e recalcula todos os registros abertos.',
            'updated'            => 'Probabilidades atualizadas para :count registro(s) aberto(s).',
        ],

        'sections' => [
            'crm' => 'CRM',
        ],

        'use-leads' => [

            'label'  => 'Leads',

            'helper' => 'Adicionar uma etapa de qualificação antes da criação de uma oportunidade.',

        ],

        'recurring-revenues' => [

            'label'  => 'Receitas recorrentes',

            'helper' => 'Definir planos e receitas recorrentes nas oportunidades.',

        ],

        'multi-teams' => [
            'label'  => 'Várias equipes',
            'helper' => 'Atribuir vendedores a várias equipes de vendas.',
        ],

        'auto-assignment' => [
            'label'        => 'Atribuição baseada em regras',
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
