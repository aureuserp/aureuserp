<?php

return [
    'tooltip' => 'Filtros',

    'fields'  => [
        'search'             => 'Pesquisar',
        'search-placeholder' => 'Pesquisar mensagens...',
        'type'               => 'Tipo',
        'date'               => 'Data',
        'sort-by'            => 'Ordenar por',
        'pinned-only'        => 'Somente fixados',
    ],
    'type-options' => [
        'all'          => 'Todos os tipos',
        'note'         => 'Notas',
        'comment'      => 'Comentários',
        'notification' => 'Notificações',
        'activity'     => 'Atividades',
    ],
    'date-options' => [
        ''          => 'Qualquer período',
        'today'     => 'Hoje',
        'yesterday' => 'Ontem',
        'week'      => 'Últimos 7 dias',
        'month'     => 'Últimos 30 dias',
        'quarter'   => 'Últimos 3 meses',
        'year'      => 'Último ano',
    ],
    'sort-options' => [
        'created_at_desc' => 'Mais recentes primeiro',
        'created_at_asc'  => 'Mais antigos primeiro',
        'updated_at_desc' => 'Atualizados recentemente',
        'priority'        => 'Prioridade',
    ],
    'actions' => [
        'apply' => 'Aplicar filtros',
    ],
];
