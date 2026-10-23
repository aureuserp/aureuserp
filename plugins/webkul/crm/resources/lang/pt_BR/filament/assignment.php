<?php

return [
    'rules' => [
        'title'         => 'Regras de atribuição',
        'match'         => 'Corresponder',
        'match-all'     => 'Todas as regras a seguir',
        'match-any'     => 'Qualquer uma das regras a seguir',
        'conditions'    => 'Condições',
        'field'         => 'Campo',
        'operator'      => 'Operador',
        'value'         => 'Valor',
        'add-condition' => 'Adicionar condição',
        'matching'      => '{1} :count registro aberto corresponde a estas regras|[0,*] :count registros abertos correspondem a estas regras',
        'no-conditions' => 'Sem condições: todos os leads são aceitos.',

        'fields' => [
            'type'                  => 'Tipo',
            'name'                  => 'Nome',
            'contact_name'          => 'Nome do contato',
            'company_name'          => 'Nome da empresa',
            'job_title'             => 'Cargo',
            'email'                 => 'E-mail',
            'phone'                 => 'Telefone',
            'website'               => 'Site',
            'street1'               => 'Rua',
            'city'                  => 'Cidade',
            'zip'                   => 'CEP',
            'state_id'              => 'Estado',
            'country_id'            => 'País',
            'stage_id'              => 'Etapa',
            'tag_ids'               => 'Etiquetas',
            'priority'              => 'Prioridade',
            'probability'           => 'Probabilidade (%)',
            'expected_revenue'      => 'Receita esperada',
            'expected_closing_date' => 'Fechamento previsto',
            'created_at'            => 'Criado em',
            'utm_campaign_id'       => 'Campanha',
            'utm_medium_id'         => 'Mídia',
            'utm_source_id'         => 'Origem',
            'referred_by'           => 'Indicado por',
        ],

        'operators' => [
            'contains'     => 'contém',
            'not_contains' => 'não contém',
            '='            => 'é igual a',
            '!='           => 'não é igual a',
            'starts_with'  => 'começa com',
            'set'          => 'está definido',
            'not_set'      => 'não está definido',
            '>'            => 'maior que',
            '>='           => 'maior ou igual a',
            '<'            => 'menor que',
            '<='           => 'menor ou igual a',
            'in'           => 'está em',
            'not_in'       => 'não está em',

            'date' => [
                '>='      => 'é em ou depois de',
                '<='      => 'é em ou antes de',
                'set'     => 'está definido',
                'not_set' => 'não está definido',
            ],
        ],
    ],

    'team' => [
        'section'                => 'Regras de atribuição',
        'section-description'    => 'Quais leads a atribuição baseada em regras dá a esta equipe.',
        'capacity'               => ':assigned leads atribuídos nos últimos 30 dias de um máximo de :max.',
        'use-leads'              => 'Leads',
        'use-opportunities'      => 'Oportunidades',
        'skip-assignment'        => 'Ignorar atribuição automática',
        'skip-assignment-helper' => 'Os leads não são atribuídos automaticamente a esta equipe.',

        'assign-leads' => [
            'label'        => 'Atribuir leads',
            'description'  => 'Atribuir agora os leads sem vendedor desta equipe aos seus membros, conforme as regras de atribuição.',
            'notification' => ':count lead(s) atribuído(s) a vendedores.',
        ],
    ],

    'members' => [
        'new-salesperson' => [
            'heading' => 'Novo vendedor',
        ],

        'title'       => 'Membros',
        'description' => 'Os vendedores desta equipe, quantos leads cada um pode receber em 30 dias e quais leads aceitam.',
        'empty'       => 'Ainda não há membros',

        'fields' => [
            'salesperson'       => 'Vendedor',
            'capacity'          => 'Máx. de leads (30 dias)',
            'capacity-helper'   => 'Número máximo de leads atribuídos a este vendedor em 30 dias.',
            'capacity-assigned' => ':count atribuídos nos últimos 30 dias.',
            'paused'            => 'Pausar atribuição',
            'paused-helper'     => 'Este vendedor não recebe novos leads automaticamente.',
            'active'            => 'Ativo',
        ],

        'columns' => [
            'salesperson' => 'Vendedor',
            'capacity'    => 'Máx. de leads (30 dias)',
            'last-month'  => 'Atribuídos (30 dias)',
            'rules'       => 'Regras',
            'paused'      => 'Pausado',
            'active'      => 'Ativo',
        ],

        'actions' => [
            'add'    => 'Adicionar membro',
            'remove' => 'Remover',
        ],

        'notifications' => [
            'added'        => 'Membro adicionado',
            'removed-from' => 'Removido de: :teams (Várias equipes está desativado).',

            'single-team' => [
                'title' => 'Um vendedor só pode estar em uma equipe',
            ],
        ],
    ],
];
