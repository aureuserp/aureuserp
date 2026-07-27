<?php

return [
    'title' => 'Etapas',

    'navigation' => [
        'title' => 'Etapas',
        'group' => 'Cargos',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informações gerais',

                'fields' => [
                    'stage-name'   => 'Nome da etapa',
                    'sort'         => 'Ordem da sequência',
                    'requirements' => 'Requisitos',
                ],
            ],

            'tooltips' => [
                'title'       => 'Dicas',
                'description' => 'Defina o rótulo personalizado para o status da candidatura.',

                'fields' => [
                    'gray-label'          => 'Rótulo cinza',
                    'gray-label-tooltip'  => 'O rótulo para o status cinza.',
                    'red-label'           => 'Rótulo vermelho',
                    'red-label-tooltip'   => 'O rótulo para o status vermelho.',
                    'green-label'         => 'Rótulo verde',
                    'green-label-tooltip' => 'O rótulo para o status verde.',
                ],
            ],

            'additional-information' => [
                'title' => 'Informações adicionais',

                'fields' => [
                    'job-positions' => 'Cargos',
                    'folded'        => 'Dobrado',
                    'hired-stage'   => 'Etapa de contratação',
                    'default-stage' => 'Etapa padrão',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'            => 'ID',
            'name'          => 'Nome da etapa',
            'hired-stage'   => 'Etapa de contratação',
            'default-stage' => 'Etapa padrão',
            'folded'        => 'Dobrado',
            'job-positions' => 'Cargos',
            'created-by'    => 'Criado por',
            'created-at'    => 'Criado em',
            'updated-at'    => 'Atualizado em',
        ],

        'filters' => [
            'name'         => 'Nome da etapa',
            'job-position' => 'Cargo',
            'folded'       => 'Dobrado',
            'gray-label'   => 'Rótulo cinza',
            'red-label'    => 'Rótulo vermelho',
            'green-label'  => 'Rótulo verde',
            'created-by'   => 'Criado por',
            'created-at'   => 'Criado em',
            'updated-at'   => 'Atualizado em',
        ],

        'groups' => [
            'job-position' => 'Cargo',
            'stage-name'   => 'Nome da etapa',
            'folded'       => 'Dobrado',
            'gray-label'   => 'Rótulo cinza',
            'red-label'    => 'Rótulo vermelho',
            'green-label'  => 'Rótulo verde',
            'created-by'   => 'Criado por',
            'created-at'   => 'Criado em',
            'updated-at'   => 'Atualizado em',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Etapas excluídos',
                        'body'  => 'As etapas foram excluídas com sucesso.',
                    ],

                    'error' => [
                        'title' => 'As etapas não puderam ser excluídas',
                        'body'  => 'As etapas não podem ser excluídas porque estão em uso no momento.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etapas excluídos',
                    'body'  => 'As etapas foram excluídas com sucesso.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Novo etapa',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'stage-name'   => 'Nome da etapa',
                    'sort'         => 'Ordem da sequência',
                    'requirements' => 'Requisitos',
                ],
            ],

            'tooltips' => [
                'title'       => 'Dicas',
                'description' => 'Defina o rótulo personalizado para o status da candidatura.',

                'entries' => [
                    'gray-label'          => 'Rótulo cinza',
                    'gray-label-tooltip'  => 'O rótulo para o status cinza.',
                    'red-label'           => 'Rótulo vermelho',
                    'red-label-tooltip'   => 'O rótulo para o status vermelho.',
                    'green-label'         => 'Rótulo verde',
                    'green-label-tooltip' => 'O rótulo para o status verde.',
                ],
            ],

            'additional-information' => [
                'title' => 'Informações adicionais',

                'entries' => [
                    'job-positions' => 'Cargo',
                    'folded'        => 'Dobrado',
                    'hired-stage'   => 'Etapa de contratação',
                    'default-stage' => 'Etapa padrão',
                ],
            ],
        ],
    ],

];
