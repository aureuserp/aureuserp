<?php

return [
    'title' => 'Alocação',

    'model-label' => 'Alocação',

    'navigation' => [
        'title' => 'Alocação',
    ],

    'global-search' => [
        'employee' => 'Funcionário',
        'time-off-type' => 'Tipo de ausência',
        'date-from' => 'Data inicial',
        'date-to' => 'Data até',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Tipo de ausência (do início da validade ao fim da validade/sem limite)',
            'time-off-type' => 'Tipo de ausência',
            'employee-name' => 'Nome do colaborador',
            'allocation-type' => 'Tipo de alocação',
            'validity-period' => 'Período de validade',
            'date-from' => 'Data inicial',
            'date-to' => 'Data até',
            'date-to-placeholder' => 'Sem limite',
            'allocation' => 'Alocação',
            'allocation-suffix' => 'Número de dias',
            'reason' => 'Motivo',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name' => 'Funcionário',
            'time-off-type' => 'Tipo de ausência',
            'amount' => 'Valor',
            'allocation-type' => 'Tipo de alocação',
            'status' => 'Status',
        ],

        'groups' => [
            'time-off-type' => 'Tipo de ausência',
            'employee-name' => 'Nome do colaborador',
            'allocation-type' => 'Tipo de alocação',
            'status' => 'Status',
            'start-date' => 'Data de início',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validar',
                    'approve' => 'Aprovar',
                ],
                'notification' => [
                    'title' => 'Alocação aprovada aprovada',
                    'body' => 'A alocação aprovada foi aprovada com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Alocação excluída',
                    'body' => 'A alocação foi excluída com sucesso.',
                ],
            ],

            'refused' => [
                'title' => 'Recusar',
                'notification' => [
                    'title' => 'Alocação recusada',
                    'body' => 'A alocação foi recusada com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Alocações excluídas',
                    'body' => 'As alocações foram excluídas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title' => 'Detalhes da alocação',
                'entries' => [
                    'name' => 'Nome',
                    'time-off-type' => 'Tipo de ausência',
                    'allocation-type' => 'Tipo de alocação',
                ],
            ],

            'validity-period' => [
                'title' => 'Período de validade',
                'entries' => [
                    'date-from' => 'Data inicial',
                    'date-to' => 'Data até',
                    'reason' => 'Motivo',
                ],
            ],
            'allocation-status' => [
                'title' => 'Status da alocação',
                'entries' => [
                    'date-to-placeholder' => 'Sem limite',
                    'allocation' => 'Número de dia(s)',
                    'allocation-value' => ':days número de dias',
                    'state' => 'Estado',
                ],
            ],
        ],
    ],
];
