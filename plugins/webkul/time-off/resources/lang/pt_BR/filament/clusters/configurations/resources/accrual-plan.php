<?php

return [
    'title' => 'Plano de acúmulo',
    'navigation' => [
        'title' => 'Plano de acúmulo',
    ],

    'form' => [
        'fields' => [
            'name' => 'Título',
            'is-based-on-worked-time' => 'É baseado no tempo trabalhado',
            'accrued-gain-time' => 'Tempo ganho acumulado',
            'carry-over-time' => 'Tempo transferido',
            'carry-over-date' => 'Data de transferência',
            'status' => 'Status',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'levels' => 'Níveis',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Plano de acúmulo excluído',
                    'body' => 'O plano de acúmulo foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Plano de acúmulo excluído',
                    'body' => 'O plano de acúmulo foi excluído com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => 'Informações básicas',
        ],

        'entries' => [
            'name' => 'Nome',
            'is-based-on-worked-time' => 'É baseado no tempo trabalhado',
            'accrued-gain-time' => 'Tempo ganho acumulado',
            'carry-over-time' => 'Tempo transferido',
            'carry-over-day' => 'Dia de transferência',
            'carry-over-month' => 'Mês de transferência',
        ],
    ],
];
