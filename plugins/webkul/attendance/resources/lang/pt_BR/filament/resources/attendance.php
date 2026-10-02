<?php

return [
    'title' => 'Presenças',

    'navigation' => [
        'title' => 'Presenças',
    ],

    'form' => [
        'employee'                   => 'Funcionário',
        'work-date'                  => 'Data de trabalho',
        'duplicate-day'              => ':employee já tem um registro de presença em :date.',
        'check-in'                   => 'Entrada',
        'check-in-not-in-work-date'  => 'A entrada deve estar dentro da data de trabalho.',
        'check-out'                  => 'Saída',
        'check-out-before-check-in'  => 'A saída deve ser após a entrada.',
        'check-out-not-in-work-date' => 'A saída deve estar dentro de 16 horas após a entrada.',
        'long-shift-warning'         => 'Atenção: este turno dura :hours horas.',
        'source'                     => 'Fonte',
        'source-manual'              => 'Manual',
    ],

    'table' => [
        'columns' => [
            'employee'   => 'Funcionário',
            'work-date'  => 'Data de trabalho',
            'check-in'   => 'Entrada',
            'check-out'  => 'Saída',
            'worked'     => 'Trabalhado',
            'source'     => 'Fonte',
            'created-at' => 'Criado em',
        ],

        'filters' => [
            'employee'          => 'Funcionário',
            'source'            => 'Fonte',
            'work-date'         => 'Data de trabalho',
            'preset'            => 'Período rápido',
            'preset-today'      => 'Hoje',
            'preset-this-week'  => 'Esta semana',
            'preset-this-month' => 'Este mês',
            'date-from'         => 'De',
            'date-to'           => 'Até',
        ],

        'toolbar-actions' => [
            'export' => 'Exportar',
        ],

        'record-actions' => [
            'add-checkout' => 'Adicionar saída',
        ],
    ],
];
