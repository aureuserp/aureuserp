<?php

return [
    'title' => 'Feriados',

    'model-label' => 'Feriado',

    'navigation' => [
        'title' => 'Feriados',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'name-placeholder' => 'Informe o nome do feriado',
            'date-from' => 'Data de início',
            'date-to' => 'Data de término',
            'color' => 'Cor',
            'calendar' => 'Calendário',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company-name' => 'Nome da empresa',
            'calendar' => 'Calendário',
            'created-by' => 'Criado por',
            'date-from' => 'Data de início',
            'date-to' => 'Data de término',
        ],

        'filters' => [
            'name' => 'Nome',
            'company-name' => 'Nome da empresa',
            'created-by' => 'Criado por',
            'date-from' => 'Data de início',
            'date-to' => 'Data de término',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'name' => 'Nome',
            'company-name' => 'Nome da empresa',
            'created-by' => 'Criado por',
            'date-from' => 'Data de início',
            'date-to' => 'Data de término',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Feriado atualizado',
                    'body' => 'O feriado foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Feriado excluído',
                    'body' => 'O feriado foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Feriados excluídos',
                    'body' => 'Os feriados foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'date-from' => 'Data de início',
            'date-to' => 'Data de término',
            'color' => 'Cor',
        ],
    ],
];
