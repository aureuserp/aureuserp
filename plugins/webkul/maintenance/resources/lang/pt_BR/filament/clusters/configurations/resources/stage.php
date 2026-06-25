<?php

return [
    'navigation' => [
        'title' => 'Etapas',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nome',
            'done' => 'Concluído',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nome',
            'done'       => 'Concluído',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'done'       => 'Concluído',
            'created-at' => 'Criado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Etapa atualizado',
                    'body'  => 'O etapa foi atualizado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Etapa excluído',
                    'body'  => 'O etapa foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Etapas excluídos',
                    'body'  => 'Os etapas foram excluídos com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informações gerais',

                'entries' => [
                    'name' => 'Nome',
                    'done' => 'Concluído',
                ],
            ],
        ],
    ],
];
