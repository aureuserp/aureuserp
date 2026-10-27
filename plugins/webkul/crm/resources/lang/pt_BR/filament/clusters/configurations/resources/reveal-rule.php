<?php

return [
    'model-label'        => 'Regra de visitantes em leads',
    'plural-model-label' => 'Regras de visitantes em leads',

    'navigation' => [
        'title' => 'Regras de visitantes em leads',
        'group' => 'Geração de leads',
    ],

    'form' => [
        'name'                     => 'Nome da regra',
        'name-placeholder'         => 'ex.: Visitantes dos EUA',
        'active'                   => 'Ativa',
        'countries'                => 'Países',
        'countries-helper'         => 'Obtido do endereço IP do visitante. Vazio = todos os países.',
        'states'                   => 'Estados',
        'states-helper'            => 'Conhecidos apenas para clientes conectados (pelo endereço). Vazio = todos os estados.',
        'url'                      => 'Expressão de URL',
        'url-placeholder'          => 'ex.: /pagina',
        'url-helper'               => 'Páginas que contam, como expressão regular no caminho. Vazio = site inteiro, "/" = apenas a página inicial, "/pricing|/shop" = páginas que contêm /pricing ou /shop.',
        'url-invalid'              => 'Informe uma expressão válida.',
        'type'                     => 'Tipo',
        'suffix'                   => 'Sufixo',
        'suffix-helper'            => 'Adicionado ao nome do lead, ex.: "Visitante do site #12 (Índia) - Preços".',
        'team'                     => 'Equipe de vendas',
        'salesperson'              => 'Vendedor',
        'tags'                     => 'Etiquetas',
        'priority'                 => 'Prioridade',
        'company'                  => 'Empresa',
        'company-helper'           => 'Vazio = a empresa do cliente ou a primeira empresa ativa.',
        'lead-for'                 => 'Rastreamento de dados',
        'industries'               => 'Setores',
        'industries-helper'        => 'Digite um nome para adicionar um setor.',
        'filter-on-size'           => 'Filtrar por porte',
        'size-min'                 => 'Porte da empresa de',
        'size-max'                 => 'Porte da empresa até',
        'employees'                => 'funcionários',
        'gdpr'                     => 'Verifique se você precisa cumprir a LGPD/GDPR para armazenar dados pessoais.',
        'contact-filter'           => 'Filtrar contatos por',
        'extra-contacts'           => 'Número de contatos',
        'extra-contacts-helper'    => 'De 1 a 5 contatos por empresa.',
        'preferred-role'           => 'Função preferida',
        'other-roles'              => 'Outras funções',
        'seniority'                => 'Senioridade',

        'sections' => [
            'traffic'          => 'Condições de tráfego do site',
            'generation'       => 'Condições de geração de oportunidades',
            'lead-generation'  => 'Condições de geração de leads',
            'contact-filter'   => 'Filtro de contatos',
            'lead-data'        => 'Dados do lead',
            'opportunity-data' => 'Dados da oportunidade',
            'settings'         => 'Configurações',
        ],
    ],

    'table' => [
        'name'          => 'Nome da regra',
        'url'           => 'Expressão de URL',
        'whole-website' => 'Site inteiro',
        'countries'     => 'Países',
        'all-countries' => 'Todos os países',
        'type'          => 'Tipo',
        'team'          => 'Equipe de vendas',
        'salesperson'   => 'Vendedor',
        'leads'         => 'Leads',
        'opportunities' => 'Oportunidades',
        'active'        => 'Ativa',
    ],

    'pages' => [
        'leads' => [
            'navigation' => 'Leads gerados',
            'title'      => 'Leads gerados: :rule',

            'columns' => [
                'name'       => 'Nome',
                'country'    => 'País',
                'stage'      => 'Etapa',
                'created-at' => 'Criado em',
            ],
        ],
    ],
];
