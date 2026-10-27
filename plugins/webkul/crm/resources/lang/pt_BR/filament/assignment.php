<?php

return [
    'rules' => [
        'title'         => 'Regras de atribuição',
        'conditions'    => 'Condições',
        'field'         => 'Campo',
        'operator'      => 'Operador',
        'value'         => 'Valor',
        'add-condition' => 'Nova regra',

        'heading-all' => 'Corresponder a todas as regras a seguir:',
        'heading-any' => 'Corresponder a qualquer uma das regras a seguir:',
        'records'     => '{0} Nenhum registro corresponde|{1} :count registro|[2,*] :count registros',
        'breakdown'   => ':leads leads · :opportunities oportunidades',

        'records-modal' => [
            'heading'     => 'Registros correspondentes',
            'description' => 'Leads e oportunidades abertos ainda não atribuídos que atendem a estas regras. Os que já estão em uma equipe (ou, para um membro, já têm vendedor) ficam de fora.',
            'empty'       => 'Nenhum lead ou oportunidade aberto atende a estas regras.',
            'showing'     => 'Mostrando os :shown mais recentes de :total registros.',
            'close'       => 'Fechar',

            'columns' => [
                'name'             => 'Nome',
                'type'             => 'Tipo',
                'contact'          => 'Contato',
                'email'            => 'E-mail',
                'salesperson'      => 'Vendedor',
                'expected-revenue' => 'Receita esperada',
                'stage'            => 'Etapa',
            ],
        ],

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
    ],

    'team' => [
        'company-helper'         => 'Empresas ativadas na barra superior. Para criar uma equipe de outra empresa, mude para ela primeiro.',
        'company-not-active'     => 'Esta empresa não está ativa. Mude para ela na barra superior primeiro.',
        'section'                => 'Regras de atribuição',
        'section-description'    => 'Quais leads a atribuição baseada em regras dá a esta equipe.',
        'capacity'               => ':assigned leads atribuídos este mês de um máximo de :max',
        'use-leads'              => 'Leads',
        'use-opportunities'      => 'Oportunidades',
        'skip-assignment'        => 'Ignorar atribuição automática',
        'skip-assignment-helper' => 'Os leads não são atribuídos automaticamente a esta equipe.',

        'assign-leads' => [
            'label'          => 'Atribuir leads',
            'heading'        => 'Atribuir leads',
            'description'    => 'Escolha os leads a distribuir. Cada um vai para o membro da equipe com mais espaço livre.',
            'submit'         => 'Atribuir',
            'records'        => 'Leads a atribuir',
            'empty'          => 'Selecione pelo menos um lead.',
            'skipped'        => 'Não foi possível distribuir :count (sem espaço livre ou as regras do membro não batem).',
            'capacity'       => 'Espaço livre: :members',
            'no-members'     => 'Nenhum membro da equipe pode receber leads agora.',
            'nothing'        => 'Nenhum lead para atribuir agora.',
            'notification'   => ':count lead(s) atribuído(s) a vendedores.',
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
            'removed-from' => 'Removido de: :teams (Vendedores em várias equipes está desativado).',

            'single-team' => [
                'title' => 'Um vendedor só pode estar em uma equipe',
            ],
        ],
    ],
];
