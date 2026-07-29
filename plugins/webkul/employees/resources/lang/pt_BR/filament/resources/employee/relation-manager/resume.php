<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title' => 'Título',
                'type' => 'Tipo',
                'name' => 'Nome',
                'type' => 'Tipo',
                'create-type' => 'Criar tipo',
                'duration' => 'Duração',
                'start-date' => 'Data de início',
                'end-date' => 'Data de término',
                'display-type' => 'Tipo de exibição',
                'description' => 'Descrição',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title' => 'Título',
            'start-date' => 'Data de início',
            'end-date' => 'Data de término',
            'display-type' => 'Tipo de exibição',
            'description' => 'Descrição',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'groups' => [
            'group-by-type' => 'Agrupar por tipo',
            'group-by-display-type' => 'Agrupar por tipo de exibição',
        ],

        'header-actions' => [
            'add-resume' => 'Adicionar currículo',
        ],

        'filters' => [
            'type' => 'Tipo',
            'start-date-from' => 'Data inicial de',
            'start-date-to' => 'Data inicial até',
            'created-from' => 'Criado a partir de',
            'created-to' => 'Criado até',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Nível de habilidade atualizado',
                    'body' => 'O nível de habilidade foi atualizado com sucesso.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Nível de habilidade criado',
                    'body' => 'O nível de habilidade foi criado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Nível de habilidade excluído',
                    'body' => 'O nível de habilidade foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Habilidades excluídas',
                    'body' => 'As habilidades foram excluídas com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title' => 'Título',
            'display-type' => 'Tipo de exibição',
            'type' => 'Tipo',
            'description' => 'Descrição',
            'duration' => 'Duração',
            'start-date' => 'Data de início',
            'end-date' => 'Data de término',
        ],
    ],
];
