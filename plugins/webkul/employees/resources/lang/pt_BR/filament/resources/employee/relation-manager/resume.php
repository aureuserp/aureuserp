<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Título',
                'name'         => 'Nome',
                'type'         => 'Tipo',
                'create-type'  => 'Criar tipo',
                'duration'     => 'Duração',
                'start-date'   => 'Data de início',
                'end-date'     => 'Data de término',
                'display-type' => 'Tipo de exibição',
                'description'  => 'Descrição',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Título',
            'start-date'   => 'Data de início',
            'end-date'     => 'Data de término',
            'display-type' => 'Tipo de exibição',
            'description'  => 'Descrição',
            'created-by'   => 'Criado por',
            'created-at'   => 'Criado em',
            'updated-at'   => 'Atualizado em',
        ],

        'groups' => [
            'group-by-type'         => 'Agrupar por tipo',
            'group-by-display-type' => 'Agrupar por tipo de exibição',
        ],

        'header-actions' => [
            'add-resume' => 'Adicionar currículo',
        ],

        'filters' => [
            'type'            => 'Tipo',
            'start-date-from' => 'Data inicial de',
            'start-date-to'   => 'Data inicial até',
            'created-from'    => 'Criado a partir de',
            'created-to'      => 'Criado até',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Currículo atualizado',
                    'body'  => 'O currículo foi atualizado com sucesso.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Currículo criado',
                    'body'  => 'O currículo foi criado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Currículo excluído',
                    'body'  => 'O currículo foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Currículos excluídos',
                    'body'  => 'Os currículos foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Título',
            'display-type' => 'Tipo de exibição',
            'type'         => 'Tipo',
            'description'  => 'Descrição',
            'duration'     => 'Duração',
            'start-date'   => 'Data de início',
            'end-date'     => 'Data de término',
        ],
    ],
];
