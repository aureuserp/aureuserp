<?php

return [
    'model-label'        => 'Plano recorrente',
    'plural-model-label' => 'Planos recorrentes',

    'navigation' => [
        'title' => 'Planos recorrentes',
    ],

    'header-actions' => [
        'create' => 'Novo plano recorrente',
    ],

    'form' => [
        'name'             => 'Nome do plano',
        'name-placeholder' => 'ex.: Mensal',
        'months'           => 'Número de meses',
        'months-helper'    => 'Quantos meses a receita recorrente cobre (Mensal = 1, Anual = 12).',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Plano',
            'months'        => 'Meses',
            'opportunities' => 'Oportunidades',
        ],
    ],

    'actions' => [
        'archive' => 'Arquivar',
    ],

    'tabs' => [
        'active'   => 'Ativos',
        'archived' => 'Arquivados',
    ],

    'notifications' => [
        'created'  => 'Plano recorrente criado',
        'updated'  => 'Plano recorrente atualizado',
        'archived' => 'Plano recorrente arquivado',
        'restored' => 'Plano recorrente restaurado',
        'deleted'  => 'Plano recorrente excluído',
    ],

    'opportunity' => [
        'recurring-revenue' => 'Receita recorrente',
        'plan'              => 'Plano recorrente',
        'mrr'               => 'Receita recorrente mensal',
    ],
];
