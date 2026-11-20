<?php

return [
    'model-label'        => 'Motivo de perda',
    'plural-model-label' => 'Motivos de perda',

    'navigation' => [
        'title' => 'Motivos de perda',
        'group' => 'Funil',
    ],

    'form' => [
        'fields' => [
            'name' => 'Descrição',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Descrição',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Motivo de perda atualizado',
                    'body'  => 'O motivo de perda foi atualizado com sucesso.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Motivo de perda restaurado',
                    'body'  => 'O motivo de perda foi restaurado com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Motivo de perda excluído',
                    'body'  => 'O motivo de perda foi excluído permanentemente.',
                ],
            ],
        ],
    ],
];
