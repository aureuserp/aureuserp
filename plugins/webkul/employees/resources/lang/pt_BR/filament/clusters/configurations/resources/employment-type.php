<?php

return [
    'title' => 'Tipos de vínculo',

    'navigation' => [
        'title' => 'Tipos de vínculo',
        'group' => 'Recrutamento',
    ],

    'form' => [
        'fields' => [
            'name' => 'Tipo de vínculo',
            'code' => 'Código',
            'country' => 'País',
        ],
    ],

    'table' => [
        'columns' => [
            'id' => 'ID',
            'name' => 'Tipo de vínculo',
            'code' => 'Código',
            'country' => 'País',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'filters' => [
            'name' => 'Tipo de vínculo',
            'country' => 'País',
            'created-by' => 'Criado por',
            'updated-at' => 'Atualizado em',
            'created-at' => 'Criado em',
        ],

        'groups' => [
            'name' => 'Tipo de vínculo',
            'country' => 'País',
            'code' => 'Código',
            'created-by' => 'Criado por',
            'created-at' => 'Criado em',
            'updated-at' => 'Atualizado em',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tipo de vínculo',
                    'body' => 'O tipo de vínculo foi editado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tipo de vínculo excluído',
                    'body' => 'O tipo de vínculo foi excluído com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tipos de vínculo excluídos',
                    'body' => 'Os tipos de vínculo foram excluídos com sucesso.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tipos de vínculo',
                    'body' => 'Os tipos de vínculo foram criados com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Tipo de vínculo',
            'code' => 'Código',
            'country' => 'País',
        ],
    ],
];
