<?php

return [
    'title' => 'Dias obrigatórios',

    'model-label' => 'Dia obrigatório',

    'navigation' => [
        'title' => 'Feriados obrigatórios',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'start-date' => 'Data de início',
            'end-date' => 'Data de término',
            'color' => 'Cor',
        ],
    ],

    'table' => [
        'columns' => [
            'name' => 'Nome',
            'company-name' => 'Nome da empresa',
            'created-by' => 'Criado por',
            'start-date' => 'Data de início',
            'end-date' => 'Data de término',
        ],

        'filters' => [
            'name' => 'Nome',
            'company-name' => 'Nome da empresa',
            'created-by' => 'Criado por',
            'start-date' => 'Data de início',
            'end-date' => 'Data de término',
        ],

        'groups' => [
            'name' => 'Nome',
            'company-name' => 'Nome da empresa',
            'created-by' => 'Criado por',
            'start-date' => 'Data de início',
            'end-date' => 'Data de término',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Dia obrigatório atualizado',
                    'body' => 'O dia obrigatório foi restaurado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Dia obrigatório excluído',
                    'body' => 'O dia obrigatório foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Dias obrigatórios excluídos',
                    'body' => 'Os dias obrigatórios foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nome',
            'start-date' => 'Data de início',
            'end-date' => 'Data de término',
            'color' => 'Cor',
        ],
    ],
];
